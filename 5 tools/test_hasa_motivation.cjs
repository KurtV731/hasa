const fs=require('node:fs'),path=require('node:path'),assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
const local=path.join(__dirname,'HASA-AKTUELL.user.js.txt');
const source=fs.readFileSync(fs.existsSync(local)?local:path.join(__dirname,'../2 src/current/HASA-AKTUELL.user.js.txt'),'utf8');
const helpers=source.slice(source.indexOf('    function prdrNormalisiereText'),source.indexOf('    // Alpha28: Rohmeldungen'));
const moduleSource=source.slice(source.indexOf('    // Alpha28: Rohmeldungen'),source.indexOf('    function parsePrdrBericht'));
(async()=>{
 const opts={headless:true};if(process.env.HASA_TEST_BROWSER)Object.assign(opts,{executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
 const browser=await chromium.launch(opts);
 try{
  const page=await browser.newPage({timezoneId:'Australia/Brisbane'});
  await page.route('https://horiversum.org/**',r=>r.fulfill({contentType:'text/html',body:'<body></body>'}));
  await page.goto('https://horiversum.org/game/main/main.php');
  await page.evaluate(({helpers,moduleSource})=>{
   const store=new Map(),auth={authenticated:true,userId:7,passwordChangeRequired:false};
   let now=Date.parse('2026-10-18T10:15:00Z');Date.now=()=>now;
   const laden=(k,d)=>store.has(k)?structuredClone(store.get(k)):d,speichern=(k,v)=>store.set(k,structuredClone(v));
   const factory=new Function('HAA_SERVER_AUTH','laden','speichern',`
    const HASA_RUNDE=8,HASA_RUNDE_SCHLUESSEL='runde8';
    const istHasaFuerSitzungAktiv=()=>true,hasaServerSchreibbereit=()=>false,esc=x=>String(x||'');
    ${helpers}\n${moduleSource}
    return {event:hasaProductionEvent,paint:maleHasaProduktion,sample:erfasseHasaProduktion,time:hasaMotivationStartzeit,active:hasaMotivationAktiv,key:STORAGE_PRODUCTION_TRACKER,register:STORAGE_EREIGNIS_REGISTER,scan:pruefeHasaEreignisse,stop:stoppeHasaLiveDaten,stopScan:stoppeHasaEreignisScanner,classify:erkenneHasaEreignis};
   `);
   const api=factory(auth,laden,speichern);
   window.video={api,store,auth,clock:x=>{now=Date.parse(x);},reload:()=>{video.api.stop();video.api.stopScan();video.api=factory(auth,laden,speichern);}};
  },{helpers,moduleSource});
  const header=`<div class="ResPlanetName">Ikan</div><div class="TickPlanetEntry" onmouseover="overlib('x',CAPTION,'1:191:6 - Ikan')"></div><table><tr>${['Eisenerz','Minerale','Treibstoff'].map(x=>'<td><div class="ResName">'+x+'</div></td>').join('')}</tr><tr>${[14796,12784,2702].map(x=>'<td><div class="ResMountNorm">100<br>('+x+'/h)</div></td>').join('')}</tr></table>`;
  const event={id:'played-1',messageId:'played-1',ownerId:'7',round:8,systemEvent:true,subject:'Motivationsvideo abgespielt',reportedTimeText:'18.10.2026 12:01:55',position:{galaxy:1,system:191,orbit:6}};
  await page.setContent(header);
  let passed=0;const test=async(name,fn)=>{await fn();passed++;console.log('PASS '+name);};
  const colors=()=>page.locator('[data-haa-rate]').evaluateAll(ns=>ns.map(n=>({color:n.style.color,weight:n.style.fontWeight,title:n.title})));
  await test('confirmed start parses in game timezone independent of browser timezone',async()=>{
   assert.equal(await page.evaluate(()=>video.api.time('18.10.2026 12:01:55')),Date.parse('2026-10-18T10:01:55Z'));
   assert.equal(await page.evaluate(()=>video.api.classify('Motivationsvideo abgespielt')),'Motivationsvideo abgespielt');
  });
  await test('availability and response request do not start a video',async()=>{
   await page.evaluate(e=>{video.api.event({...e,subject:'Motivationsvideo verfügbar',observations:[{kind:'report',rawText:'Spielen sie das Video während der Werbepause, mal sehen, ob es hilft.'}]});video.api.paint();},event);
   assert.ok((await colors()).every(n=>n.color==='rgb(170, 170, 170)'));
  });
  await test('played report colors all three hourly rates strong green without changing values',async()=>{
   await page.evaluate(e=>video.api.event(e),event);
   const c=await colors();assert.equal(c.length,3);assert.ok(c.every(n=>n.color==='rgb(0, 255, 85)'&&n.weight==='900'&&n.title.includes('13:01:55')));
   assert.match(await page.locator('[data-haa-rate="metal"]').innerText(),/14796/);
   await page.screenshot({path:process.env.HASA_MOTIVATION_SCREENSHOT||require('os').tmpdir()+'/hasa-motivation-test.png'});
  });
  await test('late reread never restarts the original hour',async()=>{
   const r=await page.evaluate(e=>{video.clock('2026-10-18T10:59:00Z');video.api.event(e);return Array.from(video.store.get(video.api.key)?Object.values(video.store.get(video.api.key)):[])[0].motivation;},event);
   assert.equal(r.endsAt,Date.parse('2026-10-18T11:01:55Z'));
  });
  await test('reload preserves the same deadline',async()=>{
   await page.evaluate(()=>{video.reload();video.api.paint();});assert.ok((await colors()).every(n=>n.color==='rgb(0, 255, 85)'));
  });
  await test('bonus readings never establish a missing normal baseline',async()=>{
   await page.evaluate(()=>video.api.sample(true));const baseline=await page.evaluate(()=>Object.values(video.store.get(video.api.key))[0].baseline);assert.deepEqual(baseline,{});
  });
  await test('at exact expiry remaining positive/negative effects reappear',async()=>{
   await page.evaluate(()=>{const all=video.store.get(video.api.key),s=Object.values(all)[0];s.active={metal:true,mineral:true};s.positive={metal:true};s.negative={mineral:true};video.store.set(video.api.key,all);video.clock('2026-10-18T11:01:55Z');video.api.paint();});
   assert.deepEqual((await colors()).map(n=>n.color),['rgb(124, 255, 124)','rgb(255, 102, 102)','rgb(170, 170, 170)']);
  });
  await test('reading expired report cannot reactivate it',async()=>{
   await page.evaluate(e=>video.api.event(e),event);assert.ok((await colors()).every(n=>n.color!=='rgb(0, 255, 85)'));
  });
  await test('wrong owner, old round, missing identity and non-system messages are rejected',async()=>{
   const count=await page.evaluate(e=>{video.store.delete(video.api.key);video.clock('2026-10-18T10:15:00Z');for(const patch of [{ownerId:8},{round:7},{messageId:null},{systemEvent:false}])video.api.event({...e,...patch});return Object.keys(video.store.get(video.api.key)||{}).length;},event);assert.equal(count,0);
  });
  await test('future, impossible and ambiguous DST times are rejected',async()=>{
   const times=await page.evaluate(()=>['31.02.2026 12:01:55','25.10.2026 02:30:00','29.03.2026 02:30:00','18.10.2026'].map(video.api.time));assert.deepEqual(times,[null,null,null,null]);
   await page.evaluate(e=>video.api.event({...e,reportedTimeText:'18.10.2026 15:00:00'}),event);assert.equal(await page.evaluate(()=>Object.keys(video.store.get(video.api.key)||{}).length),0);
  });
  await test('account and planet switches do not inherit video marking',async()=>{
   await page.evaluate(e=>video.api.event(e),event);await page.evaluate(()=>{video.auth.userId=8;video.api.paint();});assert.ok((await colors()).every(n=>n.color!=='rgb(0, 255, 85)'));
   await page.evaluate(()=>{video.auth.userId=7;document.querySelector('.TickPlanetEntry').setAttribute('onmouseover',"overlib('x',CAPTION,'1:191:7 - Ikan')");video.api.paint();});assert.ok((await colors()).every(n=>n.color!=='rgb(0, 255, 85)'));
  });
  await test('saved start report can be recovered after late authentication',async()=>{
   await page.evaluate(e=>{document.querySelector('.TickPlanetEntry').setAttribute('onmouseover',"overlib('x',CAPTION,'1:191:6 - Ikan')");video.store.delete(video.api.key);video.store.set(video.api.register,[e]);video.api.scan();},event);assert.ok((await colors()).every(n=>n.color==='rgb(0, 255, 85)'));
  });
  await test('real overview structure captures the played title and header time without opening',async()=>{
   await page.setContent(header+'<table class="MessageTable"><tr><td class="MessageTableCellLeft">18.10.2026<br>12:01:55</td><td class="MessageTableCell">Handelszentrum<div>Ikan (1:191:6)</div></td><td class="MessageTableCell"><a class="MessageReportEvent" href="?cmd=message&action=view&message_id=played-overview">Motivationsvideo abgespielt</a></td><td class="MessageTableCell"></td></tr></table>');
   await page.evaluate(()=>{video.store.delete(video.api.key);video.store.delete(video.api.register);video.api.scan();});
   assert.ok((await colors()).every(n=>n.color==='rgb(0, 255, 85)'));
   assert.equal(await page.evaluate(()=>video.store.get(video.api.register)[0].reportedTimeText),'18.10.2026 12:01:55');
  });
  await test('expiry timeout restores normal color without another scan or navigation',async()=>{
   await page.evaluate(()=>{video.clock('2026-10-18T11:01:54.980Z');video.api.paint();setTimeout(()=>video.clock('2026-10-18T11:01:55Z'),10);});
   await page.waitForFunction(()=>document.querySelector('[data-haa-rate="metal"]').style.color==='rgb(170, 170, 170)');
  });
  await test('visible countdown uses confirmed end and vanishes on expiry',async()=>{
   await page.setContent(header+'<div id="haa-video-erinnerung"></div>');
   await page.evaluate(e=>{video.clock('2026-10-18T10:15:00Z');video.store.delete(video.api.key);video.store.delete(video.api.register);video.api.event(e);video.api.paint();},event);
   assert.match(await page.locator('#haa-video-erinnerung').innerText(),/noch 47 Min.*bis 13:01/);
   await page.evaluate(()=>{video.clock('2026-10-18T11:01:55Z');video.api.paint();});
   assert.equal(await page.locator('[data-haa-video-live]').count(),0);
  });
  await page.evaluate(()=>{video.api.stop();video.api.stopScan();});
  console.log(passed+' motivation checks passed');
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});

