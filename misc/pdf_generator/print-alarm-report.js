const puppeteer = require('puppeteer');
const args = process.argv.slice(1);
const url = args[1];
const path = args[2];
let fileName = args[3];
// const TIME_OUT = parseInt(args[4]) || 8 * 1000;
const TIME_OUT = 60000;
const cookie_name = args[5] || '';
const cookie_value = args[6] || '';
const cookie_domain = args[7] || '.airdoc.com';
const zoom = 0.8;
if (path) {
  fileName = path + '/' + fileName
}

(async () => {
  const browser = await puppeteer.launch({
    args: [
      '--no-sandbox',
      '--disable-dev-shm-usage',
      '--no-first-run',
      '--no-zygote',
      '--disable-setuid-sandbox'],
    defaultViewport: {
        width: 595,
        height: 842,
        deviceScaleFactor: 1,
    },
    timeout: TIME_OUT,
    userDataDir: './puppeteer'
  },);
  // const browser = await puppeteer.launch({headless: false, userDataDir: './puppeteer'});

  const marginTop = '12mm',
    marginBottom = '12mm',
    marginRight = '8mm',
    marginLeft = '8mm';

  // footerTemplate config
  // const footerTemplateOrg = '<div style="font-size:8px;display: inline-flex;justify-content: flex-end;width: 100%;padding: 0 20px;"> <div class="pageNumber"></div>/<div class="totalPages"></div></div>'

  try {
    const page = await browser.newPage();

    await page.setCookie({
      name: cookie_name,
      value: cookie_value,
      domain: cookie_domain
    });

    page.on('response', async function (response) {
      if (response.url().includes('/api/admin/get_alarm_list')) {
        if (response.ok() && response.status() === 200) {
          let res = await response.text()
          res = JSON.parse(res)
          if (parseInt(res.error_code) !== 0) {
            await page.close();
          }
        } else {
          await page.close();
        }
      }
    })
    await page.goto(url, { waitUntil: 'networkidle0', timeout: TIME_OUT });

    // 等待Dom加载完成
    // await page.waitFor(() => {
    //     return !!document.querySelector('#reportResultDetial')
    //       && !!document.querySelector(".imgbox img").height
    //   }, { timeout: TIME_OUT }
    // );

    await page.exposeFunction('printPdf',async () => {
      // await page.pdf({
      //   path: fileName,
      //   format: 'A4',
      //   landscape: false,
      //   printBackground: true,
      //   margin: {
      //     top: marginTop,
      //     bottom: marginBottom,
      //     right: marginRight,
      //     left: marginLeft,
      //   },
      // });
      // //await page.close();
      await page.screenshot({path: fileName, fullPage: true});
    });
    
    // 使Image加载完成
    await page.evaluate(async () => {
      console.log("window.innerHeight: ", window.innerHeight);
      window.scrollBy(0, window.innerHeight);
    });

    await page.emulateMediaType('print');

    await page.evaluate(async function (zoom) {
      document.body.style.zoom = zoom;
      await window.printPdf();
    }, zoom);
    // await page.close();

  } finally {
    await browser.close();
  }
})();
