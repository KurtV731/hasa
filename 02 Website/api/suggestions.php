<?php
declare(strict_types=1);
require_once __DIR__ . '/suggestions-service.php';
header('X-Content-Type-Options: nosniff');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (!in_array($method, ['GET','POST'], true)) {
    header('Allow: GET, POST'); hasaJson(['ok'=>false,'error'=>'method_not_allowed'],405);
}
hasaAuthSession(true);
try {
    $pdo = hasaPdo();
    if (!hasaSuggestionReady($pdo)) hasaJson(['ok'=>false,'error'=>'not_ready','message'=>'Der Vorschlagsweg ist noch nicht freigeschaltet.'],503);
    if ($method === 'GET') {
        // Same token on refresh: a second tab must not silently invalidate the first.
        if (empty($_SESSION['suggestion_form']) || (int)$_SESSION['suggestion_form']['issued'] < time()-7200) {
            $_SESSION['suggestion_form']=['csrf'=>bin2hex(random_bytes(32)),'issued'=>time()];
        }
        hasaJson(['ok'=>true,'csrf'=>$_SESSION['suggestion_form']['csrf'],'min_wait_seconds'=>2,'expires_in_seconds'=>max(0,7200-(time()-(int)$_SESSION['suggestion_form']['issued']))]);
    }
    if (!hasaSuggestionOriginAllowed((string)($_SERVER['HTTP_ORIGIN']??''), hasaConfig())) hasaJson(['ok'=>false,'error'=>'origin_rejected'],403);
    if (strtolower(trim(explode(';', (string)($_SERVER['CONTENT_TYPE']??''))[0])) !== 'application/json') hasaJson(['ok'=>false,'error'=>'content_type'],415);
    $raw = file_get_contents('php://input',false,null,0,32769);
    if ($raw === false || strlen($raw)>32768) hasaJson(['ok'=>false,'error'=>'too_large'],413);
    try { $input=json_decode($raw,true,32,JSON_THROW_ON_ERROR); }
    catch (JsonException $e) { hasaJson(['ok'=>false,'error'=>'invalid_json'],400); }
    if (!is_array($input) || array_is_list($input)) hasaJson(['ok'=>false,'error'=>'invalid_json'],400);
    $form=$_SESSION['suggestion_form']??[]; $token=$input['csrf']??null;
    if (!is_string($token) || empty($form['csrf']) || !hash_equals($form['csrf'],$token) || time()-(int)($form['issued']??0)>7200) hasaJson(['ok'=>false,'error'=>'csrf_invalid'],403);
    if (time()-(int)$form['issued']<2) hasaJson(['ok'=>false,'error'=>'too_fast','retry_after'=>2],429);
    if (!is_string($input['website']??'') || ($input['website']??'')!=='') hasaJson(['ok'=>false,'error'=>'spam_rejected'],422);
    try { $data=hasaSuggestionValidate($input); }
    catch (InvalidArgumentException $e) { hasaJson(['ok'=>false,'error'=>'validation','field'=>$e->getMessage()],422); }
    $id=hasaSuggestionSave($pdo,$data,(string)($_SERVER['REMOTE_ADDR']??'unknown'),(string)(hasaConfig()['api_key']??''),time());
    if ($id===null) { header('Retry-After: 3600'); hasaJson(['ok'=>false,'error'=>'rate_limited','retry_after'=>3600],429); }
    unset($_SESSION['suggestion_form']);
    hasaJson(['ok'=>true,'id'=>$id,'message'=>'Dein Vorschlag wurde gespeichert. Vielen Dank!'],201);
} catch (Throwable $e) {
    error_log('HASA suggestion service: '.get_class($e));
    hasaJson(['ok'=>false,'error'=>'unavailable','message'=>'Speichern gerade nicht möglich. Dein Text wurde nicht bestätigt; bitte bewahre ihn auf und versuche es später erneut.'],503);
}
