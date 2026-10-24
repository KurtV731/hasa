const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const {chromium} = require(require.resolve('playwright', {paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES || __dirname]}));
const source = fs.readFileSync(process.env.HASA_TEST_SOURCE || path.join(__dirname,'../../Userscript/Aktuell/HASA-AKTUELL.user.js.txt'),'utf8');
const moduleSource = source.slice(source.indexOf('    function baueAccordionBereichHtml('),source.indexOf('    function baueBox()'));
(async()=>{
 const options={headless:true};
 if(process.env.HASA_TEST_BROWSER) Object.assign(options,{executablePath:process.env.HASA_TEST_BROWSER,args:['--no-sandbox','--single-process','--no-zygote','--disable-webgl','--disable-gpu','--disable-software-rasterizer','--use-gl=disabled']});
 const browser=await chromium.launch(options);
 try {
  const page=await browser.newPage();
  await page.setContent('<div id="haa-box" style="height:450px;width:450px;display:flex;flex-direction:column"></div>');
  await page.evaluate(moduleSource=>{
   const store={scroll:{techtree:600}};
   window.scrolling=new Function('store',`
    const STORAGE_UI_SCROLL='scroll', STORAGE_UI_AKTIVER_BEREICH='menu';
    let HAA_UI_AKTIVER_BEREICH='techtree', HAA_UI_BOX_ZU=false;
    const laden=(k,d)=>structuredClone(k in store ? store[k] : d);
    const speichern=(k,v)=>{store[k]=structuredClone(v);};
    const esc=x=>x;
    ${moduleSource}
    return {store, render(){
     const box=document.getElementById('haa-box');
     box.innerHTML=baueAccordionBereichHtml('techtree','Forschungsplanung', '<div style="height:2400px;width:900px">Voraussetzungen</div>')+baueAccordionBereichHtml('alarme','Alarme','');
     verbindeHasaUiButtons(()=>{});
    }};
   `)(store);
   scrolling.render();
  },moduleSource);
  await page.waitForTimeout(300);
  let current=await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>e.scrollTop);
  assert.equal(current,600,'saved position restored after scroll wrapper and layout');
  await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>{e.scrollTop=950;});
  await page.waitForFunction(()=>scrolling.store.scroll.techtree===950);
  await page.evaluate(()=>scrolling.render());
  await page.waitForTimeout(300);
  current=await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>e.scrollTop);
  assert.equal(current,950,'research page change keeps planner position');
  const stale=await page.evaluate(()=>{
   const old=document.querySelector('[data-haa-scroll-bereich="techtree"]');
   scrolling.render();
   old.scrollTop=0;
   old.dispatchEvent(new Event('scroll'));
   return scrolling.store.scroll.techtree;
  });
  assert.equal(stale,950,'detached container cannot overwrite saved position');
  await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>{e.scrollTop=0;});
  await page.waitForFunction(()=>scrolling.store.scroll.techtree===0);
  await page.evaluate(()=>scrolling.render());
  await page.waitForTimeout(300);
  assert.equal(await page.locator('[data-haa-scroll-bereich="techtree"]').evaluate(e=>e.scrollTop),0,'intentional scrolling to top remains saved');
  // The final row and the bottom/right controls must remain inside the box,
  // also when closed accordion summaries follow the long active section.
  await page.evaluate(()=>{
   document.getElementById('haa-box').style.cssText='height:650px;width:560px;display:flex;flex-direction:column;overflow:hidden';
   scrolling.render();
  });
  await page.waitForTimeout(300);
  for(const height of [650,400,850]){
   await page.evaluate(height=>{document.getElementById('haa-box').style.height=height+'px';window.dispatchEvent(new Event('resize'));},height);
   // Use the same layout callback as resizing the actual HASA window.
   await page.evaluate(()=>document.querySelector('#haa-box details[open]').dispatchEvent(new Event('toggle')));
   await page.waitForTimeout(100);
   const dimensions=await page.evaluate(()=>{
    const b=document.querySelector('#haa-box'),c=document.querySelector('[data-haa-scroll-bereich="techtree"]'),host=c.parentElement.querySelector('.haa-scroll-hinweise');
    const down=host.querySelector('[data-r="down"]'),right=host.querySelector('[data-r="right"]');
    c.scrollTop=0;c.scrollLeft=0;c.dispatchEvent(new Event('scroll'));
    const end=down.getBoundingClientRect().bottom;
    const result={end,boxBottom:b.getBoundingClientRect().bottom,down:down.style.display,right:right.style.display};
    c.scrollTop=c.scrollHeight;c.scrollLeft=c.scrollWidth;
    result.remaining=c.scrollHeight-c.clientHeight-c.scrollTop;
    return result;
   });
   assert.equal(dimensions.down,'inline-block','down arrow visible with remaining rows');
   assert.equal(dimensions.right,'inline-block','right arrow visible with wide content');
   assert.ok(dimensions.end<=dimensions.boxBottom+1,'bottom arrow stays inside HASA');
   assert.ok(Math.abs(dimensions.remaining)<2,'last row can be scrolled fully into view');
  }
  console.log('PASS final row and both scroll arrows reachable at three window heights');
  console.log('PASS saved scroll survives wrapper insertion and research navigation');
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});

