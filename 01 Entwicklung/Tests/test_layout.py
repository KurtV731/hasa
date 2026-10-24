from pathlib import Path
import hashlib,subprocess,json,re
root=Path(__file__).resolve().parents[2]
expected=['01 Entwicklung','02 Website','03 Dokumentation','04 Veroeffentlichung','05 Verwaltung','06 Sicherungen','07 Archiv']
for name in expected:
 if name!='06 Sicherungen':assert (root/name).is_dir(),name
for name in ['02 Website/api/galaxy.php','02 Website/index.html','01 Entwicklung/Userscript/Aktuell/HASA-AKTUELL.user.js','05 Verwaltung/Werkzeuge/server-update.ps1','05 Verwaltung/Werkzeuge/website-upload.ps1']:
 assert (root/name).is_file(),name
pairs=[('2 src/current/HASA-AKTUELL.user.js','01 Entwicklung/Userscript/Aktuell/HASA-AKTUELL.user.js'),('2 src/current/HASA-AKTUELL.user.js.txt','01 Entwicklung/Userscript/Aktuell/HASA-AKTUELL.user.js.txt'),('2 src/channels/stable/HASA.user.js','04 Veroeffentlichung/Stabil/HASA.user.js')]
for old,new in pairs:
 legacy=root/old
 if legacy.exists():assert legacy.read_bytes()==(root/new).read_bytes(),old
assert (root/pairs[0][1]).read_bytes()==(root/pairs[1][1]).read_bytes()
release=root/'04 Veroeffentlichung/Versionen/1.2.0'
expected_hash=(release/'SHA256SUMS.txt').read_text().split()[0]
assert hashlib.sha256((release/'HASA-1.2.0.user.js').read_bytes()).hexdigest()==expected_hash
assert (root/'04 Veroeffentlichung/Stabil/HASA.user.js').read_bytes()==(root/'02 Website/download/HASA-1.2.0.user.js').read_bytes()
api=(root/'05 Verwaltung/Werkzeuge/server-update.ps1').read_text()
assert "'02 Website\\api\\*'" in api and 'config.php; config.example.php' in api
web=(root/'05 Verwaltung/Werkzeuge/website-upload.ps1').read_text()
assert "'02 Website'" in web and "'download'" in web and "'index.html'" in web
for path in list((root/'.github/workflows').glob('*.yml'))+list((root/'01 Entwicklung/Tests').rglob('*'))+list((root/'05 Verwaltung').rglob('*')):
 if not path.is_file() or 'Privat' in path.parts:continue
 if path.suffix not in ('.cjs','.js','.py','.php','.ps1','.bat','.cmd','.yml'):continue
 if path==Path(__file__).resolve() or path.name=='ordner-einrichten.ps1':continue
 content=path.read_text(encoding='utf-8-sig')
 assert not re.search(r'(?:3 server|4 database|5 tools|6 website|C:\\Hasa)(?:[/\\]|\b)',content),str(path)
print('PASS new layout, release hashes, channel equality, publisher paths and no obsolete executable paths')
