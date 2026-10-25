"""Real local PHP signup/reset flow; mock sendmail writes only to a private temp inbox."""
import os,sys,tempfile,pathlib,subprocess,socket,time,http.client,re,urllib.parse,json,contextlib
api=pathlib.Path(os.environ['HASA_TEST_API']).resolve()
assert os.environ.get('CI')=='true'
with tempfile.TemporaryDirectory(prefix='hasa-signup-http-') as tmp:
 root=pathlib.Path(tmp); inbox=root/'inbox'; sender=root/'sendmail'
 sender.write_text('#!/usr/bin/env python3\nimport sys\nfrom pathlib import Path\nPath('+repr(str(inbox))+').write_bytes(sys.stdin.buffer.read())\n');sender.chmod(0o700)
 router=root/'router.php';router.write_text("<?php $_SERVER['HTTPS']='on'; $p=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH); $file="+repr(str(api))+".$p; if(!is_file($file)||pathinfo($file,PATHINFO_EXTENSION)!=='php'){http_response_code(404);exit;} require $file;")
 with socket.socket() as s:s.bind(('127.0.0.1',0));port=s.getsockname()[1]
 log=open(root/'server.log','w');server=subprocess.Popen(['php','-d','sendmail_path='+str(sender),'-S',f'127.0.0.1:{port}','-t',str(api),str(router)],stdout=log,stderr=log)
 cookie='';checks=0
 def request(path,values=None):
  global cookie
  conn=http.client.HTTPConnection('127.0.0.1',port,timeout=10);headers={'Host':'hasa.li'}
  if cookie:headers['Cookie']=cookie
  body=None
  if values is not None:body=urllib.parse.urlencode(values);headers['Content-Type']='application/x-www-form-urlencoded'
  conn.request('POST' if values is not None else 'GET',path,body,headers);r=conn.getresponse();data=r.read().decode();h=dict(r.getheaders());conn.close()
  if 'Set-Cookie' in h:cookie=h['Set-Cookie'].split(';',1)[0]
  return r.status,h,data
 def check(ok,label):
  global checks
  if not ok:raise AssertionError(label)
  checks+=1;print('PASS '+label)
 def csrf(html):return re.search(r'name="csrf" value="([a-f0-9]+)"',html).group(1)
 try:
  for _ in range(50):
   try:status,h,body=request('/register.php');break
   except OSError:time.sleep(.1)
  else:raise AssertionError('local PHP did not start')
  check(status==200 and 'Eigenes Passwort' in body,'public signup GET without prior account')
  check('no-referrer'==h.get('Referrer-Policy') and "form-action 'self'" in h.get('Content-Security-Policy',''),'onboarding pages retain referrer and form protection')
  token=csrf(body)
  status,h,body=request('/register.php',{'player_name':'HTTPNew','email':'http@example.test','password':'Http-self-password-2026','confirmation':'Http-self-password-2026','role':'root','is_user_admin':'1','csrf':'invalid'})
  check(status==403,'registration rejects missing or incorrect CSRF')
  status,h,body=request('/register.php',{'player_name':'HTTPNew','email':'http@example.test','password':'Http-self-password-2026','confirmation':'Http-self-password-2026','role':'root','is_user_admin':'1','csrf':token})
  check(status==200 and 'Bestätigungsmail' in body and inbox.exists(),'public POST generates activation mail through isolated transport')
  mail=inbox.read_text();activation=re.search(r'verify.php#([a-f0-9]{64})',mail).group(1)
  check('Http-self-password-2026' not in mail,'chosen password absent from actual generated mail')
  status,h,body=request('/verify.php');verify_csrf=csrf(body)
  (api/'signup-verify-fixture.html').write_text(body)
  check(status==200 and 'data-onboarding-form hidden' in body,'mail preview GET displays confirmation page without activation')
  status,h,body=request('/verify.php',{'csrf':verify_csrf,'setup_token':activation})
  check(status==200 and 'Dein HASA-Konto ist freigeschaltet' in body,'email-confirmation POST automatically activates player account')
  status,h,body=request('/verify.php',{'csrf':verify_csrf,'setup_token':activation})
  check(status==400,'activation replay rejected over HTTP')
  status,h,body=request('/login.php');login_csrf=csrf(body)
  status,h,body=request('/login.php',{'csrf':login_csrf,'player_name':'HTTPNew','password':'Http-self-password-2026'})
  check(status==303 and h.get('Location')=='galaxy.php','new player logs in without manual approval or forced start-password change')
  status,h,body=request('/auth-status.php');state=json.loads(body)
  check(state['authenticated'] and state['user']['role']=='player' and not state['user']['is_user_admin'] and not state['user']['can_manage_user_admins'] and not state['password_change_required'],'posted role injection cannot elevate public registration')
  status,h,body=request('/password-reset.php');reset_csrf=csrf(body)
  status,h,body=request('/password-reset.php',{'csrf':reset_csrf,'player_name':'HTTPNew','email':'http@example.test'})
  check(status==200 and 'Wenn Spielername' in body,'public reset request uses generic reply')
  mail=inbox.read_text();reset=re.search(r'password-set.php#([a-f0-9]{64})',mail).group(1)
  status,h,body=request('/password-set.php');set_csrf=csrf(body);(api/'signup-password-fixture.html').write_text(body)
  status,h,body=request('/password-set.php',{'csrf':set_csrf,'setup_token':reset,'password':'Http-new-password-2026','confirmation':'Http-new-password-2026'})
  check(status==200 and 'Dein Passwort ist gespeichert' in body,'user sets replacement password through emailed one-time link')
  status,h,body=request('/auth-status.php');check(not json.loads(body)['authenticated'],'password replacement invalidates previous session')
  status,h,body=request('/login.php');login_csrf=csrf(body)
  status,h,body=request('/login.php',{'csrf':login_csrf,'player_name':'HTTPNew','password':'Http-new-password-2026'})
  check(status==303,'replacement password works through actual login endpoint')
  status,h,body=request('/user-admin.php');check(status==403,'normal self-registered player cannot open user management')
  new_id=state['user']['id'];cookie=''
  status,h,body=request('/login.php');lead_csrf=csrf(body)
  status,h,body=request('/login.php',{'csrf':lead_csrf,'player_name':'SignupLead','password':'Lead-ci-password-2026'})
  check(status==303,'existing user leadership still logs in normally')
  status,h,body=request('/user-admin.php')
  check(status==200 and 'Registrierungsmails der letzten 24 Stunden' in body and 'Passwortlink per E-Mail senden' in body and 'Neues Startpasswort vergeben' not in body,'user administration offers mail links and delivery diagnostics instead of start passwords')
  admin_csrf=csrf(body);once=re.search(r'name="once" value="([a-f0-9]+)"',body).group(1)
  status,h,body=request('/user-admin.php',{'csrf':admin_csrf,'once':once,'target_id':str(new_id),'action':'grant-admin'})
  check(status==303,'leadership can promote a self-registered player through existing administration')
  status,h,body=request('/user-admin.php');admin_csrf=csrf(body);once=re.search(r'name="once" value="([a-f0-9]+)"',body).group(1)
  status,h,body=request('/user-admin.php',{'csrf':admin_csrf,'once':once,'target_id':str(new_id),'action':'revoke-admin'})
  check(status==303,'leadership can revoke admin rights before deleting account')
  status,h,body=request('/user-admin.php');admin_csrf=csrf(body);once=re.search(r'name="once" value="([a-f0-9]+)"',body).group(1)
  (api/'signup-admin-fixture.html').write_text(body)
  status,h,body=request('/user-admin.php',{'csrf':admin_csrf,'once':once,'target_id':str(new_id),'action':'delete','delete_confirmation':'wrong'})
  check(status==303,'delete with wrong confirmation redirects to a normal error result')
  status,h,body=request('/user-admin.php');check('angezeigten Spielernamen genau eingeben' in body,'wrong confirmation shown without deleting account')
  admin_csrf=csrf(body);once=re.search(r'name="once" value="([a-f0-9]+)"',body).group(1)
  status,h,body=request('/user-admin.php',{'csrf':admin_csrf,'once':once,'target_id':str(new_id),'action':'delete','delete_confirmation':'HTTPNew'})
  check(status==303,'confirmed account deletion completes through real endpoint')
  cookie='';status,h,body=request('/login.php');login_csrf=csrf(body)
  status,h,body=request('/login.php',{'csrf':login_csrf,'player_name':'HTTPNew','password':'Http-new-password-2026'})
  check(status==401,'deleted account cannot log in again')
  log.flush();check(activation not in (root/'server.log').read_text() and reset not in (root/'server.log').read_text(),'fragment tokens do not appear in request URL logs')
  print(f'{checks} real signup/reset HTTP checks passed; no external messages sent')
 finally:
  server.terminate();server.wait(timeout=10);log.close()
