const puppeteer = require('puppeteer');
const args = process.argv.slice(1);
 const url = args[1];
 const path = args[2];
 const fileName = args[3];
 const cookie = args[4] || '';
 const cookie_domain = args[5] || 'ikang-admin.airdoc.com';
 const height = 2236;

 (async () => {
   const browser = await puppeteer.launch({ args: [
      '--no-sandbox',
      '--disable-dev-shm-usage',
      '--no-first-run',
      '--no-zygote',
      '--disable-setuid-sandbox']});
   const page = await browser.newPage();
   await page.setCookie({
    name: 'fantastic',
    value: cookie,
    domain: cookie_domain
   });
   await page.goto(url, {timeout: 120000, waitUntil: 'networkidle0'});
   await page.emulateMediaType('screen');
   const documentSize = await page.evaluate(() => {
	 return {
		width: document.documentElement.clientWidth,
		height : document.body.clientHeight,
	  }
	});
   // await page.screenshot({ path: path + '/' + fileName, clip: { x: 0, y: 0, width: 375, height: documentSize.height }, fullPage: true });
   // await page.screenshot({ path: path + '/' + fileName, fullPage: true });
   await page.pdf({
     path: path + '/' + fileName,
     width: '375px',
     height: documentSize.height + 'px'
   });
  await browser.close();
})();
