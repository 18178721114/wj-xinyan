const puppeteer = require('puppeteer');
const args = process.argv.slice(1);
 const url = args[1];
 const path = args[2];
 const fileName = args[3];
 const cookie = args[4] || '';
 const cookie_domain = args[5] || 'ikang-admin.airdoc.com';

 (async () => {
   const browser = await puppeteer.launch({ args: [
      '--no-sandbox',
      '--disable-dev-shm-usage',
      '--no-first-run',
      '--no-zygote',
      '--disable-setuid-sandbox'],
      defaultViewport: {
        width: 375,
        height: 667
      }
    });
   const page = await browser.newPage();
   if (cookie) {
     await page.setCookie({
       name: 'fantastic',
       value: cookie,
       domain: cookie_domain
     })
   }
   await page.goto(url, {timeout: 120000, waitUntil: 'networkidle0'});
   await page.emulateMediaType('screen');
  //  await page.pdf({
  //    path: path + fileName + '.pdf',
  //    width: '375px',
  //    height: '2236px'
  //  });
  await page.screenshot({path: path + '/' + fileName, fullPage: true});
  await browser.close();
})();
