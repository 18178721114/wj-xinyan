const puppeteer = require('/lib/node_modules/puppeteer');
const args = process.argv.slice(1);
const url = args[1];
const path = args[2];
let fileName = args[3];
// const TIME_OUT = parseInt(args[4]) || 8 * 1000;
const TIME_OUT = 60000; 
if (path) {
  fileName = path + '/' + fileName
}


async function timeout(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

(async () => {
  const browser = await puppeteer.launch({
    args: [
      '--no-sandbox',
      '--disable-dev-shm-usage',
      '--no-first-run',
      '--no-zygote',
      '--disable-setuid-sandbox'],
    timeout: TIME_OUT,
    userDataDir: './puppeteer'
  },);
  // const browser = await puppeteer.launch({headless: false, userDataDir: './puppeteer'});

  try {
    const page = await browser.newPage();
    //await page.goto(url, { waitUntil: 'networkidle0', timeout: TIME_OUT });
    await page.goto(url, { waitUntil: 'networkidle0', timeout: TIME_OUT });
    // 等待Dom加载完成
    // await page.waitFor(() => {
    //     return !!document.querySelector('#reportResultDetial')
    //       && !!document.querySelector(".imgbox img").height
    //   }, { timeout: TIME_OUT }
    // );
    // 使Image加载完成
    await page.evaluate(() => {
      console.log("window.innerHeight: ", window.innerHeight);
      window.scrollBy(0, window.innerHeight);
    });
    const zoom = 1.0
    await page.evaluate(function (zoom) {
      document.body.style.zoom = zoom;
    }, zoom);
    //
    await timeout(5000);
    await page.pdf({
      path: fileName,
      format: 'A4',
      landscape: false
    });
    await page.close();
  } finally {
    await browser.close();
  }
})();
