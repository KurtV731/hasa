import urllib.parse
import json,os,re,secrets,shutil,socket,subprocess,tempfile,time,urllib.request,urllib.error,http.cookiejar
from pathlib import Path
import argparse
p=argparse.ArgumentParser(description='Nur eine eigene lokale MariaDB-Testinstanz verwenden; legt hasa_galaxy_test neu an.')
p.add_argument('--api-dir',type=Path,default=Path(__file__).resolve().parents[1]/'hasa-api')
p.add_argument('--schema',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_schema.sql')
p.add_argument('--migration',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_galaxies_migration.sql')
p.add_argument('--private-migration',type=Path,default=Path(__file__).resolve().parents[2]/'4 database/hasa_1_2_0_private_migration.sql')
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
  def get(c,path):
   status,body=jsonreq(c,path,headers={'X-HASA-Key':key});return status,json.loads(body)
  def headers(csrf):return {'Content-Type':'application/json','X-HASA-Key':key,'X-HASA-CSRF':csrf}
  for c in [alice,bob,root]:
   check(get(c,'galaxy-read.php')[1]['total']==0,'unowned legacy data leaked')
   check([int(x['galaxy']) for x in get(c,'galaxies.php')[1]['data']]==list(range(1,7)),'catalog leaked special galaxy')
   check('Green heart' not in jsonreq(c,'galaxy.php')[1],'global metadata leaked')
   for path in ['galaxy-read.php?galaxy=7&system=0','prospection-read.php?galaxy=7&system=0&orbit=1','systems.php?galaxy=7&system=0']:
    check(jsonreq(c,path,headers={'X-HASA-Key':key})[0]==403,'permission bypass '+path)
  def upload(c,csrf,label,galaxy=1,system=0):
   payload={'round':8,'galaxy':galaxy,'galaxy_name':label+' galaxy','galaxy_type':'private','system':system,'system_name':label+' system','observer':'Styl','owner_user_id':1,'visibility':'public','planets':[{'orbit':1,'name':label+' planet','type':label+'-TYPE','alliance':label+'-ALLIANCE','visibility':'alliance'}]}
   status,body=jsonreq(c,'systems.php',payload,headers(csrf));check(status==201,'write failed '+body)
  upload(alice,ac,'Alice');upload(bob,bc,'Bob');upload(root,rc,'Root')
  for c,label in [(alice,'Alice'),(bob,'Bob'),(root,'Root')]:
   status,j=get(c,'galaxy-read.php');check(status==200 and j['total']==1,'own snapshot missing')
   body=json.dumps(j);check(label+' planet' in body and all(other+' planet' not in body for other in ['Alice','Bob','Root'] if other!=label),'foreign planet leaked')
   status,j=get(c,'systems.php?galaxy=1&system=0');check(status==200 and j['data']['system_name']==label+' system','coordinate collision')
   check(get(c,'galaxy-read.php?q='+label)[1]['total']==1,'own name search missing')
   check(get(c,'galaxy-read.php?q='+('Bob' if label!='Bob' else 'Alice'))[1]['total']==0,'search leaked')
   html=jsonreq(c,'galaxy.php')[1];check(label+'-TYPE' in html and all(other+'-TYPE' not in html for other in ['Alice','Bob','Root'] if other!=label),'filter types leak')
  check(sql("SELECT COUNT(*) FROM hasa_systems WHERE owner_user_id IS NOT NULL AND visibility != 'private';")=='0','system visibility not private')
  check(sql("SELECT COUNT(*) FROM hasa_planets p JOIN hasa_systems s ON s.id=p.system_id WHERE s.owner_user_id IS NOT NULL AND p.visibility != 'private';")=='0','planet visibility not private')
  upload(alice,ac,'AliceSecret',7)
  for c in [bob,root]:
   check(get(c,'galaxy-read.php?galaxy=7&system=0')[0]==403,'special galaxy data leaked')
   check('AliceSecret' not in jsonreq(c,'galaxy.php')[1],'special metadata leaked')
  upload(alice,ac,'OnlyAlice',1,22)
  for c in [bob,root]:check(jsonreq(c,'systems.php?galaxy=1&system=22',headers={'X-HASA-Key':key})[0]==404,'direct foreign system leaked')
  for c,csrf,label,value in [(alice,ac,'Alice',42.125),(bob,bc,'Bob',21.25),(root,rc,'Root',63.5)]:
   report={'round':8,'report_key':'same-report-key','fingerprint':'same-fingerprint','visibility':'public','target':{'galaxy':1,'system':0,'orbit':1},'observer':'Styl','probe_count':100,'measurements':{'Erz':value}}
   status,body=jsonreq(c,'prospection-reports.php',report,headers(csrf));check(status==201,'report write failed '+body)
   status,j=get(c,'prospection-read.php?galaxy=1&system=0&orbit=1');check(status==200 and len(j['data'])==1 and float(j['data'][0]['measurements'][0]['value_percent'])=={'Alice':42.125,'Bob':21.25,'Root':63.5}[label],'own report missing/foreign report leaked '+str(j))
  check(sql('SELECT COUNT(*) FROM hasa_prospection_reports;')=='3','same report key overwrote another account')
  check(sql("SELECT COUNT(*) FROM hasa_prospection_reports WHERE visibility != 'private';")=='0','report visibility not private')
  for c,label in [(alice,'Alice'),(bob,'Bob'),(root,'Root')]:
   status,j=get(c,'prospection-read.php?galaxy=1&system=0&orbit=1');check(len(j['data'])==1 and float(j['data'][0]['measurements'][0]['value_percent'])=={'Alice':42.125,'Bob':21.25,'Root':63.5}[label],'report isolation after other upload')
   check(get(c,'galaxy-read.php?round=7')[1]['total']==0,'round isolation failed')
   for n in [0,256]:check(get(c,'galaxy-read.php?galaxy='+str(n))[0]==400,'invalid galaxy accepted')
  bob2,_=login(base,'Bob',password);check(get(bob2,'galaxy-read.php')[1]['total']==1,'ownership lost after new login')
  upload(bob,bc,'Far',255);check(get(bob,'galaxy-read.php?galaxy=255')[1]['total']==1,'galaxy255 missing')
  check(get(root,'galaxy-read.php?galaxy=255')[0]==403,'root bypass at255')
  check(get(bob,'galaxy-read.php?orbit=15')[0]==400,'invalid orbit accepted')
  check(get(bob,'galaxy-read.php?type=Bob')[1]['total']==0,'type filter not exact')
  check(jsonreq((base,urllib.request.build_opener(urllib.request.ProxyHandler({}))),'galaxy-read.php')[0]==401,'anonymous read accepted')
  sql('ALTER TABLE hasa_systems DROP COLUMN observed_galaxy_name;')
  status,j=get(root,'galaxy-read.php');check(status==503 and j['error']=='private_migration_required','missing migration must fail closed')
  print(str(checks)+' Integrationsprüfungen bestanden: Player/Root gleich isoliert, eigene Konten, Koordinaten-/Berichtskollisionen, Suche/Filter, Altbestände, direkte APIs, Rundentrennung, private, Migration wiederholbar und geschlossen bei fehlender Migration.')

finally:
 if server:server.terminate();server.wait()

