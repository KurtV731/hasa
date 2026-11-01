const assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||process.cwd()]}));
(async()=>{const browser=await chromium.launch({headless:true});try{
 const page=await browser.newPage({viewport:{width:1440,height:1000}});
 await page.goto('file://'+process.env.HASA_CATALOG_PREVIEW);
 assert.equal(await page.locator('caption').count(),4);
 assert.equal(await page.locator('tbody tr').count(),41);
 assert.equal(await page.locator('.hasa-event-button').count(),0);
 for(const link of await page.locator('tbody th a').all()){const url=await link.getAttribute('href');assert(url.includes('view=sheet'));}
 await page.screenshot({path:process.env.HASA_CATALOG_SCREENSHOT||'event-catalog-desktop.png',fullPage:true});
 await page.setViewportSize({width:390,height:844});
 assert(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth+2),'page should not force horizontal viewport overflow');
 const region=page.locator('.event-scroll').first();await region.evaluate(el=>el.scrollLeft=el.scrollWidth);
 assert(await region.evaluate(el=>el.scrollLeft>0),'wide planet matrix remains horizontally scrollable');
 await page.screenshot({path:'event-catalog-mobile.png',fullPage:true});
 console.log('PASS desktop/mobile matrix, sheet links and horizontal scrolling');

 if(process.env.HASA_INFO_PREVIEW){
  await page.setViewportSize({width:1920,height:1080});
  await page.goto('file://'+process.env.HASA_INFO_PREVIEW);
  assert.equal(await page.locator('thead th').count(),5);
  assert.equal(await page.locator('.event-information tbody tr').count(),1);
  assert.equal(await page.locator('.event-knowledge dl').isVisible(),false,'knowledge hidden initially');
  assert.equal(await page.locator('article').first().isVisible(),false,'raw reports hidden initially');
  const width=await page.locator('.event-information').evaluate(el=>el.getBoundingClientRect().width);
  assert(width>1700,'desktop event list must use the available width');
  await page.locator('.event-knowledge summary').click();
  assert.equal(await page.locator('.event-knowledge dl').isVisible(),true);
  await page.locator('.event-knowledge summary').click();
  await page.screenshot({path:'event-catalog-information-desktop.png',fullPage:true});
  await page.setViewportSize({width:390,height:844});
  assert(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth+2),'mobile body does not overflow');
  const tableRegion=page.locator('.event-information-scroll');
  await tableRegion.evaluate(el=>el.scrollLeft=el.scrollWidth);
  assert(await tableRegion.evaluate(el=>el.scrollLeft>0),'all five columns can be reached on mobile');
  await tableRegion.evaluate(el=>el.scrollLeft=0);
  await page.screenshot({path:'event-catalog-information-mobile.png',fullPage:true});
  console.log('PASS collapsed knowledge, five-column information list, wide desktop and scrollable mobile');
 }

 }finally{await browser.close();}})().catch(e=>{console.error(e);process.exit(1)});
