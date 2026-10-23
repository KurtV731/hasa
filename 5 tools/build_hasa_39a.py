from pathlib import Path
import hashlib,json,shutil,zipfile
root=Path(__file__).resolve().parents[1]
out=root/'delivery39a'
out.mkdir(exist_ok=True)
source=root/'2 src/current/HASA-AKTUELL.user.js'
assert source.read_bytes()==(root/'2 src/current/HASA-AKTUELL.user.js.txt').read_bytes()
assert b'1.2.0-alpha.39a' in source.read_bytes()
shutil.copyfile(source,out/'HASA-39a.user.js')
files=['event-views.php','auth.php','events-service.php','events.php']
manifest={'version':'1.2.0-alpha.39a','files':[{'name':name,'sha256':hashlib.sha256((root/'3 server/hasa-api'/name).read_bytes()).hexdigest()} for name in files]}
with zipfile.ZipFile(out/'HASA-Serverupdate-39a.zip','w',zipfile.ZIP_DEFLATED) as z:
 for name in ['HASA-Serverupdate-39a.bat','server-update-39a.ps1']:z.write(root/name,name)
 for name in files:z.write(root/'3 server/hasa-api'/name,'server39a/'+name)
 z.writestr('server39a/manifest.json',json.dumps(manifest,ensure_ascii=False,indent=2)+'\n')
 z.write(root/'1 docs/hasa-39a-test.md','LIESMICH-39a.txt')
(out/'SHA256SUMS-39a.txt').write_text(''.join(hashlib.sha256(p.read_bytes()).hexdigest()+'  '+p.name+'\n' for p in [out/'HASA-39a.user.js',out/'HASA-Serverupdate-39a.zip']))
print(out)
