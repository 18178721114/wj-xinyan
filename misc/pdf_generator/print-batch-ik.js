const puppeteer = require('puppeteer');
const args = process.argv.slice(1);
const type = args[1] || 'A4';
const cookie = args[2];
const url = args[3];
const path = args[4] || '';
const footer_text = decodeURI(args[5]) || '';
const check_id = args[6] || '';
// const TIME_OUT = parseInt(args[7]) || 4000;
const TIME_OUT = 60000;
const star = url.indexOf('://') + 3;
const end = url.indexOf('.com') + 4;
const domain = url.substring(star, end);


let fileName = check_id + '-' + type + '.pdf';
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

  const xCookie = [
    {
      'name': 'fantastic',   /* required property */
      'value': cookie,  /* required property */
      'domain': domain,
      'path': '/',                /* required property */
      'httponly': true,
      'secure': false,
      'expires': (new Date()).getTime() + (1000 * 60 * 60 * 3)   /* <-- expires in 1 hour */

    }
  ];

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
      '<span style="color:#e74478;font-weight:500;">慧心瞳 </span>&nbsp;&nbsp;报告咨询: 400-100-3999</div>' +
      '<div style="margin-left: auto; display: flex; justify-content: flex-end; align-items: center; font-size:8px;">' +
      '第<div class="pageNumber" style="display: block;"></div>页' +
      '</div>' +
      '</div>';
  }

  try {
    const page = await browser.newPage();
    await page.setCookie(...xCookie)
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
    const zoom = 0.8
    await page.evaluate(function (zoom) {
      document.body.style.zoom = zoom;
    }, zoom);
    //
    await page.pdf({
      path: fileName,
      format: type.toUpperCase(),
      landscape: false,
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
