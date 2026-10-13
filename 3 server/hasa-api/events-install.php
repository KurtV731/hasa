<?php
declare(strict_types=1);
require __DIR__.'/auth.php';
require __DIR__.'/events-service.php';
$user=hasaRequireUser();
if(!(int)($user['can_manage_user_admins']??0))hasaAuthError('Die einmalige Einrichtung ist nur für die Benutzerleitung verfügbar.',403);
$method=$_SERVER['REQUEST_METHOD']??'GET';if(!in_array($method,['GET','POST'],true)){header('Allow: GET, POST');hasaAuthError('Ungültige Anfrage.',405);}
$error='';
if($method==='POST'){
    hasaAuthCheckCsrf();
    try{foreach(hasaEventSchemaSql() as $sql)hasaPdo()->exec($sql);if(!hasaEventsAvailable(hasaPdo()))throw new RuntimeException('schema_not_ready');hasaAuthRedirect('events.php?view=asfi');}
    catch(Throwable $e){error_log('HASA Ereignislisten-Einrichtung: '.get_class($e));$error='<p class="error">Einrichtung nicht abgeschlossen. Bitte die events.1-Migration in der Datenbankverwaltung importieren. Vorhandene Daten bleiben erhalten.</p>';}
}
$body=$error.(hasaEventsAvailable(hasaPdo())?'<p>Die Ereignislisten sind eingerichtet. <a href="events.php?view=asfi">ASFI-Liste öffnen</a></p>':'<p>Zusätzliche Tabellen für Meldungen, aktuelle ASFI-Positionen und Produktionswerte anlegen. Die bestehenden Daten bleiben erhalten und die bisherigen Freigaben gelten weiter.</p><form method="post"><input type="hidden" name="csrf" value="'.hasaAuthEscape(hasaAuthCsrf()).'"><button>Ereignislisten einrichten</button></form>');
hasaAuthPage('Ereignislisten einrichten','<h1>Ereignislisten einmalig einrichten</h1>'.$body);
