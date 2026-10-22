const fs=require('fs'),path=require('path'),assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
const local=path.join(__dirname,'HASA-AKTUELL.user.js.txt');
const source=fs.readFileSync(fs.existsSync(local)?local:path.join(__dirname,'../2 src/current/HASA-AKTUELL.user.js.txt'),'utf8');
const auth=source.slice(source.indexOf('    let HAA_AUTH_TIMER = null;'),source.indexOf('    function prdrNormalisiereText'));
const returns=source.slice(source.indexOf('    async function pruefeHasaAnmeldungNachRueckkehr'),source.indexOf('    function galaxieDatenbankUrlMitStandort'));

// Verify the real release constants as well as the extracted transport functions.
const expectedEndpoints={HASA_GALASCAN_API_URL:'systems.php',HASA_PRDR_API_URL:'prospection-reports.php',
 HASA_GALAXY_DB_URL:'galaxy.php',HASA_USER_ADMIN_URL:'user-admin.php',HASA_AUTH_STATUS_URL:'auth-status.php',
 HASA_LOGIN_URL:'login.php?next=galaxy.php%3Fround%3D8',HASA_EVENT_API:'event-record.php',
 HASA_PRODUCTION_API:'production-samples.php',HASA_EVENT_LIST:'events.php'};
for(const [name,endpoint] of Object.entries(expectedEndpoints)) {
 const match=source.match(new RegExp("const "+name+" = '([^']+)';"));
 assert.ok(match,name);
 assert.equal(match[1],'https://hasa.li/api/'+endpoint,name);
}
assert.match(source,/\/\/ @connect\s+hasa\.li/);
assert.doesNotMatch(source,/serkal\.de/);
assert.match(source,/\/\/ @match\s+https:\/\/horiversum\.org\/game\/main\/main\.php\*/);
assert.match(source,/\/\/ @updateURL\s+https:\/\/raw\.githubusercontent\.com\/KurtV731\/hasa\/main\//);

(async()=>{
 const browser=await chromium.launch(process.env.HASA_TEST_BROWSER?{headless:true,executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']}:{headless:true});
 const context0=await browser.newContext();const page=await context0.newPage();const errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.route('https://horiversum.org/**',r=>r.fulfill({contentType:'text/html',body:'<body></body>'}));
 await page.goto('https://horiversum.org/game/main/main.php');
 await page.evaluate(({auth,returns})=>{
  window.calls=[];window.reply={ok:true,authenticated:false};window.fail=false;window.httpStatus=200;window.active=true;window.openCount=0;window.focusCount=0;window.renders=0;window.writes=0;window.block=false;
  window.GM_xmlhttpRequest=o=>{calls.push({url:o.url,partition:o.cookiePartition,anonymous:o.anonymous,credentials:o.withCredentials});queueMicrotask(()=>fail?o.onerror():o.onload({status:httpStatus,responseText:JSON.stringify(reply)}));};
  window.open=()=>{openCount++;if(block)return null;return window.child={closed:false,focus(){focusCount++;}};};
  const fn=new Function(`const HASA_AUTH_STATUS_URL='https://hasa.li/api/auth-status.php';const HASA_LOGIN_URL='https://hasa.li/api/login.php';function istHasaFuerSitzungAktiv(){return window.active;}function galascannerApiSchluessel(){return 'synthetic';}function baueBox(){window.renders++;}function verarbeiteGalascannerWarteschlange(){window.writes++;}function verarbeitePrdrUebertragung(){}function setInterval(){return 42;}\n`+auth+returns+`\nreturn {state:HAA_SERVER_AUTH,refresh:aktualisiereHasaServerAnmeldung,request:hasaJsonRequest,open:oeffneHasaAnmeldung,back:pruefeHasaAnmeldungNachRueckkehr,install:installiereHasaAnmeldungspruefung,ready:hasaServerSchreibbereit,hint:()=>HAA_ANMELDEHINWEIS};`);
  window.api=fn();
 },{auth,returns});
 let n=0;const test=async(name,fn)=>{await fn();n++;console.log('PASS '+name);};
 await test('HASA transport uses web first-party partition and credentials',async()=>{await page.evaluate(()=>api.refresh(true));const c=await page.evaluate(()=>calls.at(-1));assert.equal(c.partition.topLevelSite,'https://hasa.li');assert.equal(c.credentials,true);assert.equal(c.anonymous,false);});
 await test('partition is not applied to unrelated public TechTree requests',async()=>{await page.evaluate(()=>api.request({url:'https://techtree.kommandobasis.eu/objects'}));assert.equal(await page.evaluate(()=>calls.at(-1).partition),undefined);});
 await test('successful status supplies account and CSRF without local secrets',async()=>{await page.evaluate(async()=>{reply={ok:true,authenticated:true,user:{id:71,player_name:'Synthetic',is_user_admin:false},csrf:'synthetic-csrf'};await api.back(true);});assert.equal(await page.evaluate(()=>api.state.userId),71);assert.equal(await page.evaluate(()=>api.ready()),true);assert.match(await page.evaluate(()=>api.hint()),/Angemeldet als Synthetic/);});
 await test('network interruption does not erase confirmed session',async()=>{await page.evaluate(async()=>{fail=true;await api.refresh(true);});assert.equal(await page.evaluate(()=>api.state.authenticated),true);assert.match(await page.evaluate(()=>api.state.error),/Netzwerkfehler/);});
 await test('maintenance 503 retains known account and CSRF and does not authenticate a new account',async()=>{
  await page.evaluate(async()=>{fail=false;httpStatus=503;reply={ok:false,error:'maintenance',message:'HASA wird gerade umgezogen.'};await api.refresh(true);});
  assert.equal(await page.evaluate(()=>api.state.userId),71);
  assert.equal(await page.evaluate(()=>api.state.csrf),'synthetic-csrf');
  assert.equal(await page.evaluate(()=>api.state.authenticated),true);
  assert.match(await page.evaluate(()=>api.state.error),/umgezogen/);
  await page.evaluate(()=>{httpStatus=200;});
 });
 await test('server confirmed logout clears account and CSRF',async()=>{await page.evaluate(async()=>{fail=false;reply={ok:true,authenticated:false};await api.back(true);});assert.equal(await page.evaluate(()=>api.state.userId),null);assert.equal(await page.evaluate(()=>api.state.csrf),null);assert.match(await page.evaluate(()=>api.hint()),/gleichen Browser/);});
 await test('repeat login click focuses live child without another tab',async()=>{await page.evaluate(()=>{api.open();api.open();});assert.equal(await page.evaluate(()=>openCount),1);assert.equal(await page.evaluate(()=>focusCount),2);assert.equal(await page.evaluate(()=>child.opener),null);});
 await test('closed login child may be reopened',async()=>{await page.evaluate(()=>{child.closed=true;api.open();});assert.equal(await page.evaluate(()=>openCount),2);});
 await test('blocked login provides visible next step',async()=>{await page.evaluate(()=>{child.closed=true;block=true;api.open();});assert.match(await page.evaluate(()=>api.hint()),/blockiert/);});
 await test('return to game checks status immediately and only installs listeners once',async()=>{await page.evaluate(()=>{api.install();api.install();calls.length=0;reply={ok:true,authenticated:true,user:{id:72,player_name:'Return'},csrf:'new-csrf'};window.dispatchEvent(new Event('focus'));});await page.waitForFunction(()=>api.state.userId===72);assert.equal(await page.evaluate(()=>calls.length),1);});
 await test('HASA off does not poll on focus; manual status check still works',async()=>{await page.evaluate(()=>{active=false;calls.length=0;window.dispatchEvent(new Event('focus'));});await page.waitForTimeout(30);assert.equal(await page.evaluate(()=>calls.length),0);await page.evaluate(()=>api.back(true));assert.equal(await page.evaluate(()=>calls.length),1);});
 await test('password change required remains write-blocked',async()=>{await page.evaluate(async()=>{reply={ok:true,authenticated:true,password_change_required:true,user:{id:73,player_name:'Initial'},csrf:'initial'};await api.back(true);});assert.equal(await page.evaluate(()=>api.ready()),false);assert.match(await page.evaluate(()=>api.hint()),/Passwortwechsel/);});
 await test('complete userscript never starts on HASA web pages even with broad manager match',async()=>{const web=await page.context().newPage();await web.route('https://hasa.li/**',r=>r.fulfill({contentType:'text/html',body:'<body><h1>Web page</h1></body>'}));await web.goto('https://hasa.li/api/events.php');await web.addScriptTag({content:source});assert.equal(await web.locator('#haa-box').count(),0);await web.close();});
 await test('real cross-origin login tab is reused with opener detached',async()=>{
  const context=page.context();
  await context.route('https://horiversum.org/**',r=>r.fulfill({contentType:'text/html',body:'<button id="login">Anmelden</button>'}));
  await context.route('https://hasa.li/**',r=>r.fulfill({contentType:'text/html',body:'<h1>Private synthetic login</h1>'}));
  const game=await context.newPage();await game.goto('https://horiversum.org/game/main/main.php');
  await game.evaluate(({auth,returns})=>{
   window.nativeApi=new Function(`const HASA_AUTH_STATUS_URL='https://hasa.li/api/auth-status.php';const HASA_LOGIN_URL='https://hasa.li/api/login.php';function baueBox(){}\n`+auth+returns+`\nreturn {open:oeffneHasaAnmeldung,hint:()=>HAA_ANMELDEHINWEIS};`)();
   document.querySelector('#login').onclick=nativeApi.open;
  },{auth,returns});
  const childPromise=game.waitForEvent('popup');await game.locator('#login').click();const child=await childPromise;await child.waitForLoadState();
  assert.equal(await child.evaluate(()=>window.opener),null);
  const count=context.pages().length;await game.locator('#login').click();assert.equal(context.pages().length,count);
  assert.doesNotMatch(await game.evaluate(()=>nativeApi.hint()),/nicht erreichbar/);
  await child.close();await game.close();
 });
 assert.deepEqual(errors,[]);await browser.close();console.log(n+' authentication UI checks passed');
})().catch(e=>{console.error(e);process.exit(1);});
