import urllib.parse
import json,os,re,secrets,shutil,socket,subprocess,tempfile,time,urllib.request,urllib.error,http.cookiejar
from pathlib import Path
import argparse
p=argparse.ArgumentParser(description='Nur eine eigene lokale MariaDB-Testinstanz verwenden; legt hasa_galaxy_test neu an.')
p.add_argument('--api-dir',type=Path,default=Path(__file__).resolve().parents[1]/'hasa-api')
p.add_argument('--schema',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_schema.sql')
p.add_argument('--migration',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_galaxies_migration.sql')
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
 password=secrets.token_urlsafe(25)
 hashed=subprocess.run(php+['-r','echo password_hash(trim(fgets(STDIN)), PASSWORD_DEFAULT);'],input=password,text=True,capture_output=True,check=True).stdout
 for name,role in [('Styl','root'),('Alice','player'),('Bob','player')]:sql("INSERT INTO hasa_users(player_name,password_hash,role,must_change_password) VALUES ('"+name+"','"+hashed+"','"+role+"',0);")
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
  alice,ac=login(base,'Alice',password);bob,bc=login(base,'Bob',password);root,rc=login(base,'Styl',password)
  check('Hidden swarm' not in jsonreq(bob,'galaxy.php')[1], 'HTML dropdown leaks')
  check('Hidden swarm' in jsonreq(alice,'galaxy.php')[1], 'HTML dropdown missing names')
  check('Private 255' in jsonreq(root,'galaxy.php')[1], 'root HTML dropdown missing 255')
  def get(c,path):
   status,body=jsonreq(c,path);return status,json.loads(body)
  check(get(bob,'galaxies.php')[1]['data']==[{'galaxy':1,'name':'Green heart','type':'normal'}]+[{'galaxy':i,'name':None,'type':'normal'} for i in range(2,7)],'catalog leaks hidden galaxies')
  check([int(x['galaxy']) for x in get(alice,'galaxies.php')[1]['data']]==list(range(1,8)),'personal catalog missing')
  check(255 in [int(x['galaxy']) for x in get(root,'galaxies.php')[1]['data']],'root missing 255')
  for path in ['galaxy-read.php?galaxy=7&system=0','prospection-read.php?galaxy=7&system=0&orbit=1','systems.php?galaxy=7&system=0']:
   check(jsonreq(bob,path,headers={'X-HASA-Key':key})[0]==403,'direct access leaks '+path)
  check(get(bob,'galaxy-read.php')[1]['total']==1,'general search leaks')
  check(get(bob,'galaxy-read.php?q=Hidden')[1]['total']==0,'name search leaks')
  check(get(alice,'galaxy-read.php?q=Hidden')[1]['total']==1,'galaxy name search missing')
  sql("DELETE FROM hasa_galaxy_permissions WHERE user_id=(SELECT id FROM hasa_users WHERE player_name='Alice');")
  check(get(alice,'galaxy-read.php?galaxy=7&system=0')[0]==200,'seen galaxy lost after grant withdrawal')
  check(get(alice,'galaxy-read.php?round=7&galaxy=7&system=0')[0]==403,'permission crosses rounds')
  for n in [0,256]:check(get(root,'galaxy-read.php?galaxy='+str(n))[0]==400,'invalid galaxy accepted')
  payload={'round':8,'galaxy':255,'galaxy_name':'My discovery','galaxy_type':'private','system':0,'observer':'Alice','source':'galaxy_view','planets':[{'orbit':1,'name':'My discovery I'}]}
  headers={'Content-Type':'application/json','X-HASA-Key':key,'X-HASA-CSRF':bc}
  check(jsonreq(bob,'systems.php',payload,headers)[0]==201,'discovery write failed')
  check(get(bob,'galaxy-read.php?galaxy=255&system=0')[0]==200,'discovery not remembered')
  check(get(alice,'galaxy-read.php?galaxy=255&system=0')[0]==403,'spoofed observer got access')
  check(sql("SELECT last_observed_by FROM hasa_systems s JOIN hasa_galaxies g ON s.galaxy_id=g.id WHERE g.game_id=255;")=='Bob','observer identity spoofable')
  bob2,_=login(base,'Bob',password);check(get(bob2,'galaxy-read.php?galaxy=255&system=0')[0]==200,'discovery lost after login')
  check(get(bob,'galaxy-read.php?galaxy=255&q=My%20discovery')[1]['total']==1,'search includes galaxy metadata')
  check(get(bob,'prospection-read.php?galaxy=255&system=0&orbit=1')[0]==200,'authorized report access fails')
  payload['galaxy']=254;payload['galaxy_type']='empty';check(jsonreq(bob,'systems.php',payload,headers)[0]==201,'empty galaxy not accepted')
  check(sql('SELECT galaxy_type FROM hasa_galaxies WHERE round_number=8 AND game_id=254;')=='empty','empty type lost')
  report={'round':8,'report_key':'test-access-report','fingerprint':'test-fingerprint','target':{'galaxy':253,'system':0,'orbit':1},'observer':'Alice','probe_count':100,'measurements':{'Erz':42.125}}
  check(jsonreq(bob,'prospection-reports.php',report,headers)[0]==201,'prospection discovery write failed')
  check(get(bob,'prospection-read.php?galaxy=253&system=0&orbit=1')[1]['data'][0]['probe_count']==100,'own report not visible')
  check(get(alice,'prospection-read.php?galaxy=253&system=0&orbit=1')[0]==403,'report leaked to spoofed observer')
  sql('DROP TABLE hasa_galaxy_discoveries;')
  status,body=get(bob,'galaxy-read.php');check(status==503 and body['error']=='galaxy_migration_required','missing migration not explained')
  print(str(checks)+' Integrationsprüfungen bestanden: mehrere Konten, Suche, direkte APIs, Rundentrennung, Entdeckung, Identität, erneute Anmeldung, Grenzen 0/255/256.')
finally:
 if server:server.terminate();server.wait()

