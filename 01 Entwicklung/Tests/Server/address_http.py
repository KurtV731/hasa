"""Authenticated loopback endpoint tests. Never loads or contacts production."""
import json, os, pathlib, socket, subprocess, tempfile, time, urllib.request, urllib.error
api=pathlib.Path(os.environ['HASA_TEST_API'])
assert os.environ.get('CI')=='true'
seed=r'''require getenv('HASA_TEST_API').'/auth.php'; $c=hasaConfig();if($c['database']['host']!=='127.0.0.1'||$c['database']['name']!=='hasa_test_events')throw new Exception('Disposable only');$c['environment']='development';$c['api_key']=str_repeat('x',64);file_put_contents(getenv('HASA_TEST_API').'/config.php','<?php return '.var_export($c,true).';');$db=hasaPdo();$q=$db->prepare("INSERT INTO hasa_users(player_name,role,active,must_change_password,auth_version) VALUES (?,'player',1,0,1)");$q->execute(['Address-HTTP-'.bin2hex(random_bytes(4))]);$id=(int)$db->lastInsertId();session_name('HASA_SESSION');session_start();$_SESSION=['user_id'=>$id,'auth_version'=>1,'last_seen'=>time(),'logged_at'=>time(),'csrf'=>'address-csrf'];echo json_encode(['uid'=>$id,'cookie'=>'HASA_SESSION='.session_id()]);'''
identity=json.loads(subprocess.check_output(['php','-r',seed],text=True))
with socket.socket() as s:s.bind(('127.0.0.1',0));port=s.getsockname()[1]
with tempfile.TemporaryDirectory() as tmp:
 router=pathlib.Path(tmp)/'router.php';router.write_text("<?php $_SERVER['HTTPS']='on';return false;")
 log=open(pathlib.Path(tmp)/'server.log','w+')
 server=subprocess.Popen(['php','-S',f'127.0.0.1:{port}','-t',str(api),str(router)],stdout=log,stderr=log)
 base=f'http://127.0.0.1:{port}/address-book.php'
 def request(data=None,*,csrf=True,cookie=True,key=True):
  headers={'Accept':'application/json'}
  if cookie:headers['Cookie']=identity['cookie']
  if data is not None:
   headers['Content-Type']='application/json'
   if csrf:headers['X-HASA-CSRF']='address-csrf'
   if key:headers['X-HASA-Key']='x'*64
  req=urllib.request.Request(base,data=None if data is None else json.dumps(data).encode(),headers=headers)
  try:
   with urllib.request.urlopen(req,timeout=10) as r:return r.status,r.read().decode()
  except urllib.error.HTTPError as e:return e.code,e.read().decode()
 try:
  for _ in range(50):
   try:
    with socket.create_connection(('127.0.0.1',port),timeout=.2):break
   except OSError:time.sleep(.1)
  body={'action':'evidence','round':8,'expected_user_id':identity['uid'],'records':[{'name':'<img src=x>','planet':'Example','game_id':901,'galaxy':1,'system':0,'orbit':7,'source':3,'kind':'listed_planet','observed_at':time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime())}]}
  assert request(body,cookie=False)[0]==401,'anonymous write'
  assert request(body,csrf=False)[0]==403,'CSRF missing'
  assert request(body,key=False)[0]==401,'transfer key missing'
  assert request({**body,'expected_user_id':identity['uid']+1})[0]==422,'account mismatch'
  assert request({**body,'round':7})[0]==409,'old round write'
  status,payload=request(body);assert status==200 and json.loads(payload)['stored']==1,(status,payload)
  assert request(body)[0]==200,'idempotent endpoint replay'
  status,page=request();assert status==200 and '&lt;img src=x&gt;' in page and '<img src=x>' not in page,'escaped address page'
  assert request({**body,'records':[{**body['records'][0],'enemy':True}]})[0]==422,'private relationship rejected'
  print('PASS address HTTP: authentication, CSRF, key, owner binding, round, durable receive, replay, escaped page, forbidden relationship')
 finally:
  server.terminate();server.wait(timeout=10);log.close()
