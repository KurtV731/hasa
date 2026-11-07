<?php
declare(strict_types=1);
require_once __DIR__.'/auth.php';require_once __DIR__.'/address-service.php';
$user=hasaRequireUser();
if(empty($user['can_manage_user_admins']))hasaAuthError('Nur für die Hauptadministration.',403);
$message='Legt ausschließlich die drei neuen Adressbuchtabellen an; bestehende Spieldaten bleiben erhalten.';
if(($_SERVER['REQUEST_METHOD']??'GET')==='POST'){hasaAuthCheckCsrf();hasaAddressInstall(hasaPdo());$message='Adressbuch eingerichtet. HASA kann jetzt automatisch Nachweise speichern.';}
hasaAuthPage('Adressbuch einrichten','<p>'.hasaAuthEscape($message).'</p><form method="post"><input type="hidden" name="csrf" value="'.hasaAuthEscape(hasaAuthCsrf()).'"><button>Adressbuchtabellen einrichten</button></form><p><a href="address-book.php">Zum Adressbuch</a></p>');
