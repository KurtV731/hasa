import urllib.parse
import json,os,re,secrets,shutil,socket,subprocess,tempfile,time,urllib.request,urllib.error,http.cookiejar
from pathlib import Path
import argparse
p=argparse.ArgumentParser(description='Nur eine eigene lokale MariaDB-Testinstanz verwenden; legt hasa_galaxy_test neu an.')
p.add_argument('--api-dir',type=Path,default=Path(__file__).resolve().parents[1]/'hasa-api')
p.add_argument('--schema',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_schema.sql')
p.add_argument('--migration',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_galaxies_migration.sql')
p.add_argument('--private-migration',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_private_migration.sql')
p.add_argument('--admin-migration',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_useradmin_migration.sql')
p.add_argument('--browser-check',type=Path)
p.add_argument('--mysql-bin',default='mariadb')
p.add_argument('--php-bin',default='php')
p.add_argument('--php-option',action='append',default=[])
p.add_argument('--port',type=int,default=3307)
a=p.parse_args()
if a.port==3306: p.error('Bitte eine separate Testinstanz auf einem anderen Port verwenden.')
mysql=[a.mysql_bin,'--no-defaults','--host=127.0.0.1','--protocol=TCP','--port='+str(a.port),'-uroot','-N','-B']
php=[a.php_bin]+a.php_option
checks=0
def check(v,msg):
 global checks
 assert v,msg
 checks+=1
def sql(s,db=True):
 r=subprocess.run(mysql+(['hasa_galaxy_test'] if db else []),input=s,text=True,capture_output=True);assert r.returncode==0,r.stderr;return r.stdout.strip()
def jsonreq(c,path,data=None,headers=None):
 req=urllib.request.Request(c[0]+path,data=json.dumps(data).encode() if data is not None else None,headers=headers or {})
 try:r=c[1].open(req)
 except urllib.error.HTTPError as e:r=e
 return r.status,r.read().decode()
def login(base,name,password):
 jar=http.cookiejar.CookieJar();op=urllib.request.build_opener(urllib.request.ProxyHandler({}),urllib.request.HTTPCookieProcessor(jar))
 body=op.open(base+'login.php').read().decode();assert 'name="csrf"' in body,body;csrf=re.search(r'name="csrf" value="([a-f0-9]+)"',body)[1]
 op.open(urllib.request.Request(base+'login.php',data=urllib.parse.urlencode({'csrf':csrf,'player_name':name,'password':password}).encode())).read()
 c=(base,op);status,body=jsonreq(c,'auth-status.php');j=json.loads(body);check(j['authenticated'],name+' login');return c,j['csrf']
server=None
try:
 sql('DROP DATABASE IF EXISTS hasa_galaxy_test; CREATE DATABASE hasa_galaxy_test;',False)
 sql(a.schema.read_text())
 sql(a.migration.read_text())
 sql(a.migration.read_text())
 sql(a.private_migration.read_text())
 sql(a.private_migration.read_text())
 password=secrets.token_urlsafe(25)
 hashed=subprocess.run(php+['-r','echo password_hash(trim(fgets(STDIN)), PASSWORD_DEFAULT);'],input=password,text=True,capture_output=True,check=True).stdout
 for name,role in [('Styl','root'),('Alice','player'),('Bob','player')]:sql("INSERT INTO hasa_users(player_name,password_hash,role,must_change_password) VALUES ('"+name+"','"+hashed+"','"+role+"',0);")
 sql(a.admin_migration.read_text())
 sql(a.admin_migration.read_text())
 sql("INSERT INTO hasa_users(player_name,password_hash,role,must_change_password) VALUES ('ServerRoot','"+hashed+"','root',0);")
 sql("INSERT INTO hasa_galaxies(round_number,game_id,display_name,galaxy_type) VALUES (8,1,'Green heart','normal'),(8,7,'Hidden swarm','swarm'),(8,255,'Private 255','private'),(7,7,'Old round secret','private');")
 sql("INSERT INTO hasa_systems(galaxy_id,system_number,system_name,last_observed_at) SELECT id,0,'Testsystem',UTC_TIMESTAMP() FROM hasa_galaxies;")
 sql("INSERT INTO hasa_planets(system_id,orbit_position,planet_name,last_observed_at) SELECT id,1,'Planet I',UTC_TIMESTAMP() FROM hasa_systems;")
 sql("INSERT INTO hasa_galaxy_permissions(galaxy_id,user_id,granted_by_user_id) SELECT g.id,u.id,1 FROM hasa_galaxies g,hasa_users u WHERE g.round_number=8 AND g.game_id=7 AND u.player_name='Alice';")
 with tempfile.TemporaryDirectory() as tmp:
  d=Path(tmp);shutil.copytree(a.api_dir,d/'api')
  dbpw=secrets.token_hex(24);key=secrets.token_hex(32)
  sql("CREATE USER IF NOT EXISTS 'hasa_galaxy_test'@'localhost' IDENTIFIED BY '"+dbpw+"'; ALTER USER 'hasa_galaxy_test'@'localhost' IDENTIFIED BY '"+dbpw+"'; GRANT ALL ON hasa_galaxy_test.* TO 'hasa_galaxy_test'@'localhost';",False)
  (d/'api/config.php').write_text("<?php return ['environment'=>'testing','database'=>['host'=>'127.0.0.1','port'=>"+str(a.port)+",'name'=>'hasa_galaxy_test','user'=>'hasa_galaxy_test','password'=>'"+dbpw+"'],'api_key'=>'"+key+"'];")
  with socket.socket() as sock:sock.bind(('127.0.0.1',0));port=sock.getsockname()[1]
  (d/'sessions').mkdir();log=open(d/'php-test.log','w');server=subprocess.Popen(php+['-d','session.save_path='+str(d/'sessions')]+['-S','127.0.0.1:'+str(port),'-t',str(d/'api')],stdout=log,stderr=log);time.sleep(.2);base='http://127.0.0.1:'+str(port)+'/'
  styl,sc=login(base,'Styl',password);alice,ac=login(base,'Alice',password);bob,bc=login(base,'Bob',password);root,rc=login(base,'ServerRoot',password)
  def form(c,path,data):
   req=urllib.request.Request(c[0]+path,data=urllib.parse.urlencode(data).encode())
   try:r=c[1].open(req)
   except urllib.error.HTTPError as e:r=e
   return r.status,r.read().decode(),dict(r.headers)
  def admin(c):return jsonreq(c,'user-admin.php')
  def tokens(body):return {k:re.search('name="'+k+'" value="([a-f0-9]+)"',body)[1] for k in ['csrf','once']}
  def action(c,kind,name='',id=0,extra=None):
   status,html=admin(c);check(status==200,'admin unavailable')
   data=tokens(html)|{'action':kind,'player_name':name,'target_id':id}|(extra or {})
   return form(c,'user-admin.php',data),data
  check(sql("SELECT CONCAT(role,':',is_user_admin,':',can_manage_user_admins) FROM hasa_users WHERE player_name='Styl';")=='player:1:1','Styl transition')
  check(sql("SELECT id FROM hasa_users WHERE player_name='Styl';")=='1','Styl ID changed')
  check('Benutzerverwaltung' in jsonreq(styl,'galaxy.php')[1],'admin link missing')
  for c in [alice,bob,root]:check(admin(c)[0]==403,'implicit administration rights')
  check('Benutzerverwaltung' not in jsonreq(alice,'galaxy.php')[1],'ordinary player link')
  (status,body,h),data=action(styl,'create','First',extra={'role':'root','is_user_admin':1,'created_by_user_id':2})
  check(status==200 and 'PPW:4711' in body,'first sequential startpassword')
  check(h.get('Cache-Control')=='no-store','startpassword cacheable')
  check('PPW:4711' not in admin(styl)[1],'password shown twice')
  check(form(styl,'user-admin.php',data)[0]==409,'double submission accepted')
  check(sql("SELECT CONCAT(role,':',is_user_admin,':',created_by_user_id) FROM hasa_users WHERE player_name='First';")=='player:0:1','injected role/admin/owner')
  check(sql("SELECT password_hash FROM hasa_users WHERE player_name='First';").startswith(('$2y$','$argon2')),'password not hashed')
  check(action(styl,'create','First')[0][1].find('bereits vergeben')>=0,'duplicate not explained')
  check(action(styl,'create','Second')[0][1].find('PPW:4712')>=0,'failed transaction consumed password sequence')
  first,fc=login(base,'First','PPW:4711')
  check(jsonreq(first,'galaxy-read.php')[0]==403,'initial password allows game access')
  check('Bitte dein Startpasswort' in admin(first)[1],'initial password allows administration')
  status,body,h=form(first,'password-change.php',{'csrf':fc,'current_password':'PPW:4711','new_password':password,'confirmation':password});check(status==200,'required password change failed')
  check(admin(first)[0]==403,'ordinary new player can administer')
  aid=int(sql("SELECT id FROM hasa_users WHERE player_name='Alice';"));bid=int(sql("SELECT id FROM hasa_users WHERE player_name='Bob';"));fid=int(sql("SELECT id FROM hasa_users WHERE player_name='First';"))
  check('erteilt' in action(styl,'grant-admin',id=aid)[0][1],'grant failed')
  alice,ac=login(base,'Alice',password)
  check('First' not in admin(alice)[1] and 'Second' not in admin(alice)[1],'admin list outside scope')
  check('PPW:4713' in action(alice,'create','AliceChild')[0][1],'delegated create failed')
  child=int(sql("SELECT id FROM hasa_users WHERE player_name='AliceChild';"))
  for kind in ['reset-password','block','unblock','grant-admin','revoke-admin']:
   check('Verwaltungsbereich' in action(alice,kind,id=fid)[0][1],'cross-scope action '+kind)
  check('Nur die Benutzerleitung' in action(alice,'grant-admin',id=child)[0][1],'delegate delegated admin')
  check(sql("SELECT is_user_admin FROM hasa_users WHERE id="+str(child))=='0','privilege escalation')
  check('gesperrt' in action(alice,'block',id=child)[0][1],'own block')
  check(sql('SELECT active FROM hasa_users WHERE id='+str(child))=='0','block not stored')
  check('entsperrt' in action(alice,'unblock',id=child)[0][1],'own unblock')
  check('PPW:4714' in action(alice,'reset-password',id=child)[0][1],'own reset sequential password')
  check('geschützt' in action(styl,'block',id=1)[0][1] or 'eigene Konto' in action(styl,'block',id=1)[0][1],'self protection')
  rid=int(sql("SELECT id FROM hasa_users WHERE player_name='ServerRoot';"))
  check('geschützt' in action(styl,'reset-password',id=rid)[0][1],'root password unprotected')
  status,html=admin(styl);data=tokens(html)|{'action':'create','player_name':'CSRF'};data['csrf']='bad';check(form(styl,'user-admin.php',data)[0]==403,'CSRF accepted')
  check(sql("SELECT COUNT(*) FROM hasa_users WHERE player_name='CSRF';")=='0','CSRF created account')
  check(action(styl,'create','<script>alert(1)</script>')[0][1].find('&lt;script&gt;')>=0,'name not escaped')
  check('entzogen' in action(styl,'revoke-admin',id=aid)[0][1],'revoke failed')
  alice,ac=login(base,'Alice',password);check(admin(alice)[0]==403,'revoked access retained')
  sql(a.admin_migration.read_text());check(sql('SELECT is_user_admin FROM hasa_users WHERE id='+str(aid))=='0','repeat migration restores grant')
  check(sql("SELECT CONCAT(role,':',is_user_admin) FROM hasa_users WHERE player_name='Styl';")=='player:1','repeat Styl migration')
  check(json.loads(jsonreq(styl,'galaxy-read.php')[1])['total']==0,'useradmin sees legacy private data')
  report={'round':8,'galaxy':1,'system':22,'system_name':'Bob private','planets':[{'orbit':1,'name':'Bob secret'}]}
  check(jsonreq(bob,'systems.php',report,{'Content-Type':'application/json','X-HASA-Key':key,'X-HASA-CSRF':bc})[0]==201,'private fixture upload')
  check(json.loads(jsonreq(styl,'galaxy-read.php')[1])['total']==0,'Styl admin gained foreign game read')
  check(json.loads(jsonreq(root,'galaxy-read.php')[1])['total']==0,'Root gained foreign game read')
  check(all(b'PPW:' not in f.read_bytes() for f in (d/'sessions').glob('*') if f.is_file()),'plaintext start password persisted in session')
  if a.browser_check: subprocess.run(['node',str(a.browser_check)],input=json.dumps({'base':base,'password':password}),text=True,check=True)
  print(str(checks)+' Benutzeradmin-Prüfungen bestanden: Styl-Umstellung, Browserverwaltung, Delegation/Scope, Passwortfolge/Hashes/Pflichtwechsel, CSRF, Wiederholung, Sperren, geschützte Konten, XSS und private Datensicht.')

finally:
 if server:server.terminate();server.wait()

