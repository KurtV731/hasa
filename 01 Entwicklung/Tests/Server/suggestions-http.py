import os,pathlib,tempfile,subprocess,socket,time,http.client,json,re,urllib.parse
assert os.environ.get('CI')=='true'
api=pathlib.Path(os.environ['HASA_TEST_API']).resolve()
with tempfile.TemporaryDirectory() as tmp:
    root=pathlib.Path(tmp)
    router=root/'router.php'
    router.write_text("<?php $_SERVER['HTTPS']='on';$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);if($path==='/test-login'){require "+repr(str(api/'auth.php'))+";hasaAuthSession();$_SESSION['user_id']=(int)($_GET['id']??1);$_SESSION['auth_version']=1;$_SESSION['logged_at']=time();$_SESSION['last_seen']=time();echo 'fixture';exit;} $f="+repr(str(api))+".$path;if(!is_file($f)){http_response_code(404);exit;}require $f;")
    with socket.socket() as sock:sock.bind(('127.0.0.1',0));port=sock.getsockname()[1]
    log=open(root/'http.log','w');server=subprocess.Popen(['php','-S',f'127.0.0.1:{port}',str(router)],stdout=log,stderr=log)
    cookie=''
    def req(path='/suggestions.php',value=None,origin='https://hasa.li',method=None,raw=None,form=False):
        global cookie
        conn=http.client.HTTPConnection('127.0.0.1',port,timeout=8)
        headers={'Host':'hasa.li','Origin':origin}
        if cookie:headers['Cookie']=cookie
        body=raw
        if value is not None:body=urllib.parse.urlencode(value) if form else json.dumps(value)
        if body is not None:headers['Content-Type']='application/x-www-form-urlencoded' if form else 'application/json'
        conn.request(method or ('POST' if body is not None else 'GET'),path,body,headers)
        r=conn.getresponse();content=r.read().decode();h=dict(r.getheaders());conn.close()
        if 'Set-Cookie' in h:cookie=h['Set-Cookie'].split(';')[0]
        return r.status,h,content
    def check(ok,label):
        assert ok,label
        print('PASS '+label)
    try:
        for _ in range(60):
            try:status,h,body=req();break
            except OSError:time.sleep(.1)
        else:raise AssertionError('server start')
        check(status==200,'GET supplies anonymous session token')
        token=json.loads(body)['csrf'];check('no-store' in h['Cache-Control'],'responses not cached')
        payload={'csrf':token,'category':'idee','subject':'HTTP Test <script>','message':'Dieser Test enthält nur Beispieldaten. <script>alert(1)</script>','name':'','contact':'','website':'','consent':True}
        check(req(value=payload,origin='https://evil.test')[0]==403,'foreign origin rejected')
        check(req(value={**payload,'csrf':'bad'})[0]==403,'CSRF rejected')
        check(req(value=payload)[0]==429,'minimum form time enforced')
        time.sleep(2.1)
        check(req(value={**payload,'website':'spam'})[0]==422,'honeypot rejected honestly')
        check(req(value={**payload,'consent':False})[0]==422,'consent required')
        check(req(raw='{"broken":')[0]==400,'malformed JSON rejected')
        check(req(raw='x'*32769)[0]==413,'body size limited')
        status,h,body=req(value=payload);check(status==201 and json.loads(body)['ok'] and len(json.loads(body)['id'])==32,'real HTTP POST committed to MariaDB')
        stored_id=json.loads(body)['id']
        check(req(value=payload)[0]==403,'single use token prevents duplicate retry')
        check(req(method='DELETE')[0]==405,'unsupported method rejected')
        check(req('/suggestions-admin.php')[0]==303,'anonymous administration redirects to login')
        req('/test-login?id=2');check(req('/suggestions-admin.php')[0]==403,'ordinary player cannot read suggestions')
        req('/test-login?id=1');status,h,body=req('/suggestions-admin.php')
        check(status==200 and '&lt;script&gt;' in body and '<script>alert(1)</script>' not in body,'admin reads persisted text with HTML escaping')
        csrf=re.search(r'name="csrf" value="([a-f0-9]+)"',body).group(1)
        check(req('/suggestions-admin.php',{'csrf':'bad','action':'delete','id':stored_id,'confirm':'delete'},form=True)[0]==403,'deletion requires CSRF')
        check(req('/suggestions-admin.php',{'csrf':csrf,'action':'delete','id':stored_id},form=True)[0]==422,'deletion requires explicit confirmation')
        check(req('/suggestions-admin.php',{'csrf':csrf,'action':'delete','id':stored_id,'confirm':'delete'},form=True)[0]==303,'confirmed deletion succeeds')
        status,h,body=req('/suggestions-admin.php');check('HTTP Test' not in body,'deleted suggestion gone')
    finally:
        server.terminate();server.wait(timeout=5);log.close()
