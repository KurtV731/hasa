const fs=require('node:fs'),assert=require('node:assert/strict');
const {chromium}=require(require.resolve('playwright',{paths:[process.env.CODEX_PRIMARY_RUNTIME_NODE_MODULES||__dirname]}));
(async()=>{
 const browser=await chromium.launch({headless:true});
 try{
  const token='a'.repeat(64),api=process.env.HASA_TEST_API;
  for(const [file,path] of [['signup-verify-fixture.html','verify.php'],['signup-password-fixture.html','password-set.php']]){
   const page=await browser.newPage({viewport:{width:390,height:844}});const errors=[];page.on('pageerror',e=>errors.push(e.message));
   const html=fs.readFileSync(api+'/'+file,'utf8');let posts=0;
   await page.route('**/*',r=>{if(r.request().method()==='POST')posts++;r.fulfill({contentType:'text/html; charset=utf-8',body:html});});
   await page.goto('https://hasa.li/api/'+path+'#'+token);
   assert.equal(await page.locator('[data-onboarding-token]').inputValue(),token);
   assert.equal(await page.evaluate(()=>location.hash),'');assert.equal(await page.locator('[data-onboarding-form]').isVisible(),true);assert.equal(posts,0,'preview does not submit activation');
   assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth),true,'mobile form fits viewport');assert.deepEqual(errors,[]);
   await page.goto('https://hasa.li/api/'+path);assert.equal(await page.locator('[data-onboarding-form]').isVisible(),false,'missing token cannot show usable form');
   await page.close();console.log('PASS fragment handoff, no premature activation and mobile layout: '+path);
  }
 const adminPage=await browser.newPage({viewport:{width:390,height:844}});
  const adminHtml=fs.readFileSync(api+'/signup-admin-fixture.html','utf8');await adminPage.route('**/*',r=>r.fulfill({contentType:'text/html; charset=utf-8',body:adminHtml}));await adminPage.goto('https://hasa.li/api/user-admin.php');
  const form=adminPage.locator('form[data-hasa-user-actions]').filter({has:adminPage.locator('option[value="delete"]')}).first();await form.locator('..').evaluate(el=>el.open=true);
  const action=form.locator('[data-hasa-action]'),email=form.locator('input[name="email"]'),deletion=form.locator('[data-hasa-delete-fields]'),confirmation=form.locator('[name="delete_confirmation"]');
  assert.equal(await email.isVisible(),false);assert.equal(await email.isDisabled(),true);
  await action.selectOption('reset-password');assert.equal(await email.isVisible(),true);assert.equal(await email.isDisabled(),false);await email.fill('invalid-email');
  await action.selectOption('block');assert.equal(await email.isVisible(),false);assert.equal(await email.isDisabled(),true);assert.equal(await form.evaluate(f=>f.checkValidity()),true,'hidden email cannot prevent unrelated account action');
  await action.selectOption('delete');assert.equal(await deletion.isVisible(),true);assert.equal(await email.isVisible(),false);assert.equal(await form.evaluate(f=>f.checkValidity()),false,'deletion requires account name');
  const name=await confirmation.getAttribute('data-expected-name');await confirmation.fill('wrong');assert.equal(await form.evaluate(f=>f.checkValidity()),false);await confirmation.fill(name);assert.equal(await form.evaluate(f=>f.checkValidity()),true);
  await action.selectOption('reset-password');assert.equal(await deletion.isVisible(),false);assert.equal(await confirmation.isDisabled(),true);
  assert.equal(await adminPage.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth),true,'admin action form fits mobile width');await adminPage.close();console.log('PASS action-specific email, disabled validation and named deletion confirmation');
 const rootHtml=fs.readFileSync(require('node:path').join(__dirname,'../../../02 Website/index.html'),'utf8');
  for(const width of [390,800,1200]){
   const page=await browser.newPage({viewport:{width,height:900}});await page.route('**/*',r=>r.fulfill({contentType:'text/html; charset=utf-8',body:rootHtml}));await page.goto('https://hasa.li/');
   assert.ok(await page.locator('a[href="/api/register.php"]').count()>=2,'registration reachable from navigation and main content');
   assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth<=window.innerWidth),true,'registration navigation fits viewport '+width);await page.close();
  }
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
