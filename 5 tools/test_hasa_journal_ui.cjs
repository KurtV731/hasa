const fs=require('fs'),assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
const fixture=process.env.HASA_TEST_JOURNAL_HTML;
assert.ok(fixture,'actual PHP/MariaDB fixture is required');
(async()=>{
 const browser=await chromium.launch();
 const page=await browser.newPage({viewport:{width:1200,height:900}}),errors=[];
 page.on('pageerror',e=>errors.push(e.message));
 await page.route('https://hasa.test/**',r=>r.fulfill({contentType:'text/html; charset=utf-8',body:fs.readFileSync(fixture,'utf8')}));
 await page.goto('https://hasa.test/events.php?view=hub&round=8');
 assert.equal(await page.locator('#hub .hasa-event-button').count()>4,true);
 const video=page.locator('#hub .hasa-event-button').filter({hasText:'Motivationsvideo abgespielt'});
 assert.match(await video.innerText(),/31/);assert.match(await video.innerText(),/Letzte 30 Tage: 28/);
 const url=new URL(await video.getAttribute('href'),'https://hasa.test/');
 assert.equal(url.searchParams.get('subject'),'Motivationsvideo abgespielt');assert.equal(url.searchParams.get('view'),'event');
 assert.equal(await page.locator('#hub a').filter({hasText:'Aktuelle ASFI-Liste'}).count(),1);
 assert.equal(await page.locator('#hub a').filter({hasText:'Gildensitzungen'}).count(),1);
 assert.equal(await page.locator('#journal article').count(),25);
 assert.match(await page.locator('#journal h1').innerText(),/Gesamt: 31/);
 assert.equal(await page.locator('script,img').count(),0);
 assert.equal(await page.locator('#hub a').filter({hasText:'Laufende Events nach Planet'}).count(),1);
 assert.equal(await page.locator('#active section').count(),2);
 assert.match(await page.locator('#active').innerText(),/Private Auswertung/);
 assert.match(await page.locator('#active').innerText(),/Motivationsvideo läuft/);
 assert.match(await page.locator('#active').innerText(),/Ende nicht gemeldet/);
 const next=page.locator('#journal a').filter({hasText:'Weitere Meldungen'});
 assert.equal(new URL(await next.getAttribute('href'),'https://hasa.test/').searchParams.get('offset'),'25');
 for(const width of [1200,390]){
  await page.setViewportSize({width,height:900});
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),true,'no horizontal page overflow at '+width);
  assert.equal(await video.isVisible(),true);
 }
 assert.deepEqual(errors,[]);await browser.close();console.log('PASS actual PHP event hub and dated journal at desktop and mobile widths');
})().catch(e=>{console.error(e);process.exit(1);});
