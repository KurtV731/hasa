<?php
declare(strict_types=1);
require __DIR__.'/auth.php';
require __DIR__.'/events-service.php';
$user=hasaRequireUser(true);
if(($_SERVER['REQUEST_METHOD']??'GET')!=='POST'){header('Allow: POST');hasaJson(['ok'=>false,'error'=>'method_not_allowed'],405);}
hasaRequireApiKey();hasaAuthCheckCsrf(true);$pdo=hasaPdo();
if(!hasaEventsAvailable($pdo))hasaJson(['ok'=>false,'error'=>'events_migration_required'],503);
try{$stored=hasaStoreProduction($pdo,$user,hasaReadJson());}
catch(InvalidArgumentException $e){hasaJson(['ok'=>false,'error'=>$e->getMessage()],400);}
catch(DomainException $e){hasaJson(['ok'=>false,'error'=>$e->getMessage()],409);}
hasaJson(['ok'=>true,'stored'=>$stored],201);
