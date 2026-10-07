#!/usr/bin/env python3
"""Integrationstest nur gegen eine eigene lokale MariaDB-Testinstanz.
Keine produktive config.php verwenden. Siehe Installationsdokument.
"""
import argparse
import hashlib
import http.cookiejar
import json
import os
from pathlib import Path
import re
import secrets
import shutil
import socket
import subprocess
import tempfile
import time
import urllib.error
import urllib.parse
import urllib.request

p = argparse.ArgumentParser()
p.add_argument('--api-dir', required=True, type=Path)
p.add_argument('--schema', required=True, type=Path)
p.add_argument('--migration', required=True, type=Path)
p.add_argument('--php-bin', default='php')
p.add_argument('--php-option', action='append', default=[])
p.add_argument('--mysql-bin', default='mariadb')
p.add_argument('--socket')
p.add_argument('--port', type=int, default=3306)
a = p.parse_args()
php = [a.php_bin] + a.php_option
mysql = [a.mysql_bin, '--no-defaults', '-uroot', '-N', '-B'] + (['--socket='+a.socket] if a.socket else ['--host=127.0.0.1','--protocol=TCP','--port='+str(a.port)])
checks = 0

def check(value, message):
    global checks
    assert value, message
    checks += 1

def sql(text, db=True, success=True):
    result = subprocess.run(mysql + (['hasa_auth_test'] if db else []), input=text, text=True, capture_output=True)
    if success:
        assert result.returncode == 0, result.stderr
    else:
        check(result.returncode != 0, 'SQL-Schutz fehlt')
    return result.stdout.strip()

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, *args, **kwargs): return None

class Client:
    def __init__(self, base):
        self.base = base
        self.jar = http.cookiejar.CookieJar()
        self.opener = urllib.request.build_opener(urllib.request.ProxyHandler({}), urllib.request.HTTPCookieProcessor(self.jar), NoRedirect())
    def request(self, path, data=None, headers=None):
        req = urllib.request.Request(self.base+'/'+path, data=data, headers=headers or {})
        try: r = self.opener.open(req, timeout=10)
        except urllib.error.HTTPError as e: r = e
        return r.status, r.headers, r.read().decode()
    def form(self, path, fields):
        return self.request(path, urllib.parse.urlencode(fields).encode(), {'Content-Type':'application/x-www-form-urlencoded'})
    def csrf(self, path):
        status, _, body = self.request(path)
        check(status == 200, 'Formular nicht verfügbar: '+path)
        m = re.search(r'name="csrf" value="([a-f0-9]+)"', body)
        check(m is not None, 'CSRF fehlt')
        return m[1]
    def status(self):
        status, _, body = self.request('auth-status.php')
        check(status == 200, 'Status fehlgeschlagen')
        return json.loads(body)
    def login(self, name, secret):
        return self.form('login.php', {'csrf':self.csrf('login.php'), 'player_name':name, 'password':secret})
    def cookie(self):
        return next((c.value for c in self.jar if c.name == 'HASA_SESSION'), None)

sql('DROP DATABASE IF EXISTS hasa_auth_test; CREATE DATABASE hasa_auth_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;', False)
# Erst Grundschema ohne Auth-Erweiterung, damit die Bestandsmigration wirklich geprüft wird.
schema = a.schema.read_text().split('-- Benutzeranmeldung (auch bei frischer Installation).')[0]
sql(schema)
sql("INSERT INTO hasa_users (player_name) VALUES ('Styl'), ('Altbestand');")
root_id = sql("SELECT id FROM hasa_users WHERE player_name='Styl';")
sql('INSERT INTO hasa_galaxies (round_number,game_id,owner_user_id) VALUES (7,1,'+root_id+');')
sql(a.migration.read_text())
sql(a.migration.read_text())
check(sql("SELECT COUNT(*) FROM hasa_users WHERE password_hash IS NOT NULL;") == '0', 'Migration legt unerlaubt Kontenzugänge an')
check(sql("SELECT id FROM hasa_users WHERE player_name='Styl';") == root_id, 'Bestands-ID verändert')

with tempfile.TemporaryDirectory(prefix='hasa-auth-test-') as temp:
    d = Path(temp)
    shutil.copytree(a.api_dir, d/'api')
    key = secrets.token_hex(32)
    db_secret = secrets.token_hex(24)
    sql("CREATE USER IF NOT EXISTS 'hasa_auth_test'@'localhost' IDENTIFIED BY '"+db_secret+"'; ALTER USER 'hasa_auth_test'@'localhost' IDENTIFIED BY '"+db_secret+"'; GRANT ALL ON hasa_auth_test.* TO 'hasa_auth_test'@'localhost';", False)
    (d/'api/config.php').write_text("<?php return ['environment'=>'testing','database'=>['host'=>'"+("localhost" if a.socket else "127.0.0.1")+"','port'=>"+str(a.port)+",'name'=>'hasa_auth_test','user'=>'hasa_auth_test','password'=>'"+db_secret+"','charset'=>'utf8mb4'],'api_key'=>'"+key+"'];")
    sessions = d/'sessions'; sessions.mkdir()
    opts = ['-d', 'session.save_path='+str(sessions)] + (['-d','pdo_mysql.default_socket='+a.socket] if a.socket else [])
    def admin(args, secret, ok=True):
        result = subprocess.run(php+opts+[str(d/'api/tools/user-admin.php')]+args, input=secret, capture_output=True, text=True)
        check((result.returncode == 0) == ok, 'CLI-Kontenaktion: '+str(args)+' '+result.stderr)
        return result.stdout
    root_start = secrets.token_urlsafe(20)
    root_password = secrets.token_urlsafe(24)
    player_password = secrets.token_urlsafe(24)
    admin(['init-root'], root_start+'\n'+root_start+'\n')
    admin(['init-root'], root_start+'\n'+root_start+'\n', False)
    check(sql("SELECT id FROM hasa_users WHERE role='root';") == root_id, 'Styl-ID nicht übernommen')
    check(sql('SELECT owner_user_id FROM hasa_galaxies WHERE round_number=7 AND game_id=1;') == root_id, 'Datenzuordnung verändert')
    with socket.socket() as s:
        s.bind(('127.0.0.1',0)); port = s.getsockname()[1]
    log = open(d/'php.log', 'w+')
    server = subprocess.Popen(php+opts+['-S','127.0.0.1:'+str(port),'-t',str(d/'api')], stdout=log, stderr=log)
    try:
        base = 'http://127.0.0.1:'+str(port)
        anon = Client(base)
        for _ in range(100):
            try: anon.request('auth-status.php'); break
            except urllib.error.URLError: time.sleep(.05)
        for endpoint in ['galaxy-read.php','prospection-read.php','systems.php','prospection-reports.php']:
            check(anon.request(endpoint)[0] == 401, 'Anonymer API-Zugriff: '+endpoint)
        check(anon.request('galaxy.php?galaxy=2&system=42')[0] == 303, 'Webzugriff ohne Login')
        check(anon.request('tools/user-admin.php')[0] == 404, 'CLI über HTTP erreichbar')
        status, headers, body = anon.request('login.php')
        check('HttpOnly' in headers.get('Set-Cookie','') or any(c.has_nonstandard_attr('HttpOnly') for c in anon.jar), 'HttpOnly fehlt')
        check('no-store' in headers.get('Cache-Control',''), 'Cache-Schutz fehlt')
        check(anon.form('login.php', {'player_name':'Styl','password':root_start})[0] == 403, 'Login ohne CSRF')
        check(anon.login('Styl', root_start+'falsch')[0] == 401, 'Falsches Passwort angenommen')
        before = anon.cookie()
        status, headers, _ = anon.login('Styl', root_start)
        check(status == 303 and headers['Location'] == 'password-change.php', 'Pflichtwechsel root')
        check(anon.cookie() != before, 'Session-ID beim Login unverändert')
        check(anon.status()['password_change_required'] is True, 'Pflichtwechselstatus fehlt')
        for endpoint in ['galaxy-read.php','prospection-read.php','systems.php','prospection-reports.php']:
            check(anon.request(endpoint)[0] == 403, 'Startpasswort schaltet Modul frei')
        admin(['create-player','ZuFrueh','Styl'], root_start+'\n', False)
        csrf = anon.csrf('password-change.php')
        check(anon.form('password-change.php', {'csrf':csrf,'current_password':root_start,'new_password':'kurz','confirmation':'kurz'})[0] == 400, 'Kurzes Passwort angenommen')
        before = anon.cookie()
        status, headers, _ = anon.form('password-change.php', {'csrf':csrf,'current_password':root_start,'new_password':root_password,'confirmation':root_password})
        check(status == 303, 'root-Passwortwechsel fehlgeschlagen')
        check(anon.cookie() != before, 'Session-ID beim Wechsel unverändert')
        check('galaxy=2' in headers['Location'] and 'system=42' in headers['Location'], 'Standort beim Login verloren')
        check(anon.status()['user']['role'] == 'root', 'Styl nicht root')
        check(anon.request('galaxy-read.php')[0] == 200, 'Daten lesen nach Wechsel')
        check(anon.request('galaxy.php?round=8')[0] == 200, 'Webansicht Runde 8 nicht verfügbar')
        status, _, body = anon.request('galaxy-read.php?round=8')
        payload = json.loads(body)
        check(status == 200 and payload['round'] == 8 and payload['data'] == [], 'Runde 8 beginnt nicht leer')
        # Keine Klartextpasswörter, echtes password_verify und alte Passwörter ungültig.
        root_hash = sql("SELECT password_hash FROM hasa_users WHERE role='root';")
        check(root_hash.startswith('$argon2id$') or root_hash.startswith('$2y$'), 'Kein sicherer Hash')
        check(root_start not in root_hash and root_password not in root_hash, 'Klartext in Hashfeld')
        fresh = Client(base)
        check(fresh.login('Styl', root_start)[0] == 401, 'Startpasswort nach Wechsel gültig')
        created = admin(['create-player','TestPilot','Styl'], root_password+'\n')
        check('PPW:4711' in created, 'Startsequenz beginnt falsch')
        player_id = sql("SELECT id FROM hasa_users WHERE player_name='TestPilot';")
        check(sql('SELECT role FROM hasa_users WHERE id='+player_id+';') == 'player', 'Playerrolle fehlt')
        second = admin(['create-player','ZweiterPilot','Styl'], root_password+'\n')
        check('PPW:4712' in second, 'Sequenz nicht fortlaufend')
        player = Client(base)
        check(player.login('TestPilot', 'ppw:4711')[0] == 401, 'Passwortschreibung ignoriert')
        check(player.login('TestPilot', 'PPW4711')[0] == 401, 'Doppelpunkt ignoriert')
        check(player.login('TestPilot', 'PPW:4711')[0] == 303, 'Player-Startlogin')
        check(player.request('galaxy-read.php')[0] == 403, 'Player-Pflichtwechsel umgangen')
        csrf = player.csrf('password-change.php')
        check(player.form('password-change.php', {'csrf':csrf,'current_password':'PPW:4711','new_password':player_password,'confirmation':player_password})[0] == 303, 'Playerwechsel')
        check(player.request('galaxy-read.php')[0] == 200, 'Player kann nicht lesen')
        admin(['create-player','Unberechtigt','TestPilot'], player_password+'\n', False)
        state = player.status()
        check(state['user']['role'] == 'player', 'Playerrechte falsch')
        system_payload = json.dumps({'round':8,'galaxy':1,'system':0,'observed_at':'2026-10-07T12:00:00Z','observer':'TestPilot','visibility':'private','source':'galaxy_view','planets':[{'orbit':1,'name':'Runde Acht','type':'unknown'}]}).encode()
        base_headers = {'Content-Type':'application/json','X-HASA-Key':key}
        check(player.request('systems.php', system_payload, base_headers)[0] == 403, 'Transfer ohne CSRF')
        headers = {**base_headers, 'X-HASA-CSRF':state['csrf']}
        status, _, body = player.request('systems.php', system_payload, headers)
        check(status == 201 and json.loads(body)['stored']['round'] == 8, 'Runde-8-System nicht gespeichert')
        report_payload = json.dumps({'round':8,'report_key':'round8-test-1','fingerprint':'round8-fingerprint-1','target':{'galaxy':1,'system':0,'orbit':1,'name':'Runde Acht'},'observed_at':'2026-10-07T12:05:00Z','observer':'TestPilot','probe_count':100,'probe_type':{'name':'PRDR','code':'PRDR'},'planet_type':{'name':'unknown','code':'unknown'},'measurements':{'Eisenerz':42.5}}).encode()
        status, _, body = player.request('prospection-reports.php', report_payload, headers)
        check(status == 201 and json.loads(body)['stored']['round'] == 8, 'Runde-8-Bericht nicht gespeichert')
        check(player.request('systems.php', b'{}', headers)[0] == 400, 'Alter Client ohne Runde schreibt weiter')
        status, _, body = player.request('galaxy-read.php?round=8&galaxy=1&system=0')
        data = json.loads(body)
        check(status == 200 and data['round'] == 8 and len(data['data']) == 1, 'Runde-8-System nicht lesbar')
        status, _, body = player.request('prospection-read.php?round=8&galaxy=1&system=0&orbit=1')
        reports = json.loads(body)
        check(status == 200 and reports['round'] == 8 and len(reports['data']) == 1, 'Runde-8-Bericht nicht lesbar')
        status, _, body = player.request('galaxy-read.php?round=7&galaxy=1')
        legacy = json.loads(body)
        check(status == 200 and legacy['round'] == 7 and len(legacy['data']) == 0, 'Rundenbestände vermischt')
        admin(['rename',player_id,'Styl','UmbenannterPilot'],root_password+'\n')
        check(sql("SELECT id FROM hasa_users WHERE player_name='UmbenannterPilot';") == player_id, 'Umbenennung verändert ID')
        check(player.status()['user']['player_name'] == 'UmbenannterPilot', 'Sitzung kennt neuen Namen nicht')
        admin(['block',player_id,'Styl'],root_password+'\n')
        check(player.request('galaxy-read.php')[0] == 401, 'Sperre wirkt nicht auf bestehende Sitzung')
        check(player.login('UmbenannterPilot',player_password)[0] == 401, 'Gesperrtes Konto meldet sich an')
        admin(['unblock',player_id,'Styl'],root_password+'\n')
        check(player.login('UmbenannterPilot',player_password)[0] == 303, 'Entsperrung')
        reset = admin(['reset-password',player_id,'Styl'],root_password+'\n')
        check('PPW:4713' in reset, 'Reset verwendet kein neues Startpasswort')
        check(player.request('galaxy-read.php')[0] == 401, 'Reset lässt alte Sitzung aktiv')
        check(player.login('UmbenannterPilot',player_password)[0] == 401, 'Altes Passwort nach Reset gültig')
        check(player.login('UmbenannterPilot','PPW:4713')[0] == 303, 'Reset-Startlogin')
        check(player.request('galaxy-read.php')[0] == 403, 'Reset erzwingt keinen Wechsel')
        admin(['block',root_id,'Styl'],root_password+'\n',False)
        admin(['reset-password',root_id,'Styl'],root_password+'\n',False)
        for query in ['UPDATE hasa_users SET id=99999 WHERE id='+player_id, 'DELETE FROM hasa_users WHERE id='+root_id, "UPDATE hasa_users SET role='player' WHERE id="+root_id, 'UPDATE hasa_users SET active=0 WHERE id='+root_id]:
            sql(query+';',success=False)
        before_counter = sql("SELECT meta_value FROM hasa_meta WHERE meta_key='player_start_password_next';")
        sql(a.migration.read_text())
        check(sql("SELECT meta_value FROM hasa_meta WHERE meta_key='player_start_password_next';") == before_counter, 'Migration setzt Passwortzähler zurück')
        check(sql("SELECT password_hash FROM hasa_users WHERE role='root';") == root_hash, 'Migration überschreibt Passwort')
        check(anon.request('logout.php')[0] == 200, 'Logoutbestätigung fehlt')
        check(anon.form('logout.php', {})[0] == 403, 'Logout ohne CSRF')
        check(anon.form('logout.php', {'csrf':anon.csrf('logout.php')})[0] == 303, 'Logout fehlgeschlagen')
        check(anon.request('galaxy-read.php')[0] == 401, 'Sitzung nach Logout gültig')
        limited = Client(base)
        for _ in range(10): check(limited.login('NichtVorhanden','falsch')[0] == 401, 'Loginbegrenzung zu früh')
        check(limited.login('NichtVorhanden','falsch')[0] == 429, 'Loginbegrenzung fehlt')
        expiry = Client(base)
        check(expiry.login('Styl', root_password)[0] == 303, 'Login für Ablaufprüfung')
        session_id = expiry.cookie()
        def expire(field, seconds):
            code = "session_name('HASA_SESSION'); session_id('"+session_id+"'); session_start(); $_SESSION['"+field+"']=time()-"+str(seconds)+"; session_write_close();"
            run = subprocess.run(php+opts+['-r',code],capture_output=True,text=True)
            check(run.returncode == 0, 'Sitzungsablauf vorbereiten')
        expire('last_seen',7201)
        check(expiry.request('galaxy-read.php')[0] == 401, 'Inaktive Sitzung bleibt gültig')
        check(expiry.login('Styl', root_password)[0] == 303, 'Erneuter Login')
        session_id = expiry.cookie()
        expire('logged_at',43201)
        check(expiry.request('galaxy-read.php')[0] == 401, 'Absolute Sitzungsgrenze fehlt')
        # Frische Installation vollständig aus der neuen Gesamtdatei.
        sql('DROP DATABASE hasa_auth_test; CREATE DATABASE hasa_auth_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;', False)
        sql(a.schema.read_text())
        private_config = (d/'api/config.php').read_text()
        (d/'api/config.php').unlink()
        offline = admin(['prepare-root-sql'], root_start+'\n'+root_start+'\n')
        (d/'api/config.php').write_text(private_config)
        check(root_start not in offline, 'Offline-Einrichtung enthält Klartext')
        sql(offline)
        offline_hash = sql("SELECT password_hash FROM hasa_users WHERE role='root';")
        sql(offline)
        check(sql("SELECT password_hash FROM hasa_users WHERE role='root';") == offline_hash, 'Offline-Import überschreibt vorhandenes root-Passwort')
        check(sql("SELECT COUNT(*) FROM hasa_users WHERE player_name='Styl' AND role='root' AND must_change_password=1;") == '1', 'Offline-Einrichtung falsch')
        config_path = d/'api/config.php'
        config = config_path.read_text()
        config_path.write_text(config.replace("'testing'", "'production'"))
        check(Client(base).request('login.php')[0] == 400, 'Produktivbetrieb erlaubt HTTP')
        cookie_probe = "$_SERVER['HTTPS']='on'; $_SERVER['SCRIPT_NAME']='/hasa/login.php'; require "+repr(str(d/'api/auth.php'))+"; hasaAuthSession(); echo json_encode(session_get_cookie_params());"
        probe = subprocess.run(php+opts+['-r',cookie_probe],capture_output=True,text=True)
        check(probe.returncode == 0, 'HTTPS-Sitzungsprüfung fehlgeschlagen')
        params = json.loads(probe.stdout)
        check(params['secure'] and params['httponly'] and params['samesite']=='Lax' and params['path']=='/hasa/', 'Produktive Cookieparameter falsch')
        config_path.write_text(config)
        check(sql("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='hasa_auth_test' AND TABLE_NAME='hasa_users' AND COLUMN_NAME='auth_version';") == '1', 'Frisches Schema ohne Auth')
        check(sql("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='hasa_auth_test' AND TABLE_NAME='hasa_galaxies' AND COLUMN_NAME='round_number';") == '1', 'Frisches Schema ohne Rundentrennung')
        print(str(checks)+' Prüfungen bestanden (PHP/MariaDB/HTTP).')
    finally:
        server.terminate(); server.wait(timeout=10)
        log.close()
sql("DROP DATABASE hasa_auth_test; DROP USER 'hasa_auth_test'@'localhost';", False)
