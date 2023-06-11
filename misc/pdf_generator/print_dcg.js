const puppeteer = require('puppeteer');
const args = process.argv.slice(1);
const url = args[1];
const path = args[2];
let fileName = args[3];
let product = '护心宝';

// const TIME_OUT = parseInt(args[4]) || 8 * 1000;
const TIME_OUT = 60000;
const footer_text = decodeURI(args[5]) || '';
const telephone_text = decodeURI(args[6]) || '';
const zoom = decodeURI(args[7]) || 0.8;
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
  let footerTemplateOrg = '' +
    '<div style="font-size:2mm;color:#394868;width:100%; display: inline-flex; align-items: center;' +
    'margin-left: ' + marginLeft + ';' +
    'margin-right: ' + marginRight + ';' +
    '">' +
    '<div style="justify-content: flex-end; margin-left: auto; display: flex;">' +
    '<div class="pageNumber" style="display: block;"></div>/<div class="totalPages" style="display: block;"></div>' +
    '</div>' +
    '</div>';

    if (footer_text) {
      footerTemplateOrg = '' +
        '<div style="font-size:2mm;color:#394868;width:100%; display: inline-flex; align-items: center;' +
        'margin-left: ' + marginLeft + ';' +
        'margin-right: ' + marginRight + ';' +
        '">' +
        '<span style="justify-content: flex-start;  margin-right: auto">' + footer_text + '</span>' +
        '<div style="justify-content: center; margin: auto">' +
        '<span style="color:#1a8ea2;font-weight:500;">'+product+'</span>&nbsp;&nbsp;' + telephone_text + '</div>' +
        '<div style="margin-left: auto; display: flex; justify-content: flex-end; align-items: center; font-size:8px;">' +
        '第<div class="pageNumber" style="display: block;"></div>页' +
        '</div>' +
        '</div>';
    }
  
    try {
      const page = await browser.newPage();
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
      // const zoom = 0.8
      await page.evaluate(function (zoom) {
        document.body.style.zoom = zoom;
      }, zoom);
      //
     var sleep = function(time) {
      var startTime = new Date().getTime() + parseInt(time, 10);
      while(new Date().getTime() < startTime) {}
  };
  
      sleep(20000);
      await page.pdf({
        path: fileName,
        format: 'A4',
        landscape: false,
        printBackground: true,
        displayHeaderFooter: true,
        headerTemplate: '<div style="font-size:10px; display: inline-flex;justify-content: center;width: 100%; padding: 0 20px;"><div class="title"></div></div>',
        footerTemplate: footerTemplateOrg,
        margin: {
          top: marginTop,
          bottom: marginBottom,
          right: marginRight,
          left: marginLeft,
        },
      });
      await page.close();
    } finally {
      await browser.close();
    }
  })();
                