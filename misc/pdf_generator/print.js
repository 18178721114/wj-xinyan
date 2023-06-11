const puppeteer = require('puppeteer');
const http = require('http');
const args = process.argv.slice(1);
const url = args[1];
const path = args[2];
let fileName = args[3];
let product = '慧心瞳';
if (args[4]) {
  product = args[4];
}
// const TIME_OUT = parseInt(args[4]) || 8 * 1000;
const TIME_OUT = 60000;
const footer_text = decodeURI(args[5]) || '';
const telephone_text = decodeURI(args[6]) || '';
const zoom = decodeURI(args[7]) || 0.8;
if (path) {
  fileName = path + '/' + fileName
}

let port = Math.floor(Math.random() * 4) + 9220;
http.get('http://172.17.170.135:' + port + '/json/version', (response) => {
  let todo = '';
  // called when a data chunk is received.
  response.on('data', (chunk) => {
    todo += chunk;
  });
  // called when the complete response is received.
  response.on('end', () => {
    let webSocketDebuggerUrl = '';
    webSocketDebuggerUrl = JSON.parse(todo).webSocketDebuggerUrl;
    if (webSocketDebuggerUrl) {
      (async () => {
        const browser = await puppeteer.connect({ browserWSEndpoint: webSocketDebuggerUrl });
        let marginBottom = '12mm';
        const marginTop = '12mm',
          marginRight = '8mm',
          marginLeft = '8mm';
        // 隐藏所有
        let footerTemplateOrg =
          '<div style="font-size:2mm;color:#394868;width:100%; display: inline-flex; align-items: center;' +
          'margin-left: ' + marginLeft + ';' +
          'margin-right: ' + marginRight + ';' +
          '">' +
          '<div style="justify-content: flex-end; margin-left: auto; display: flex;">' +
          '</div>' +
          '</div>';
        if (footer_text === '') { // 隐藏简版，隐藏完整版和简版
          footerTemplateOrg =
            '<div style="font-size:2mm;color:#394868;width:100%; display: inline-flex; align-items: center;' +
            'margin-left: ' + marginLeft + ';' +
            'margin-right: ' + marginRight + ';' +
            '">' +
            '<div style="justify-content: flex-end; margin-left: auto; display: flex;">' +
            '<div class="pageNumber" style="display: block;"></div>/<div class="totalPages" style="display: block;"></div>' +
            '</div>' +
            '</div>';
        } else if (parseInt(footer_text) !== 3 && product === '鹰瞳医疗') { // 不隐藏
          marginBottom = '16mm';
          footerTemplateOrg = '<div style="width:100%; position:relative;padding:0 1%;box-sizing:border-box;">' +
            `<div style="color: #e7e8ec; margin: 0px 4mm 2px; font-size:2mm; padding-bottom: 3px; border-bottom: 1px solid #e7e8ec;">注：本报告仅供临床医生参考。</div>` +
            '<div style="font-size:2mm;color:#394868; display: flex; align-items: center;' +
            'margin-left: 4mm;' +
            'margin-right: 4mm;' +
            '">' +
            '<span style="justify-content: flex-start;  margin-right: auto">' + footer_text + '</span>' +
            '<div style="justify-content: center; margin: auto">' +
            '<span style="color:#e74478;font-weight:500;">' + product + '</span>&nbsp;&nbsp;' + telephone_text + '</div>' +
            '<div style="margin-left: auto; display: flex; justify-content: flex-end; align-items: center; font-size:8px;">' +
            '第<div class="pageNumber" style="display: block;"></div>页' +
            '</div>' +
            '</div></div>';
        } else if (parseInt(footer_text) !== 3) { // 不隐藏
          footerTemplateOrg = '' +
            '<div style="font-size:2mm;color:#394868;width:100%; display: inline-flex; align-items: center;' +
            'margin-left: ' + marginLeft + ';' +
            'margin-right: ' + marginRight + ';' +
            '">' +
            '<span style="justify-content: flex-start;  margin-right: auto">' + footer_text + '</span>' +
            '<div style="justify-content: center; margin: auto">' +
            '<span style="color:#e74478;font-weight:500;">' + product + '</span>&nbsp;&nbsp;' + telephone_text + '</div>' +
            '<div style="margin-left: auto; display: flex; justify-content: flex-end; align-items: center; font-size:8px;">' +
            '第<div class="pageNumber" style="display: block;"></div>页' +
            '</div>' +
            '</div>';
        }

        try {
          const page = await browser.newPage();
          page.on('response', async function (response) {
            if (response.url().includes('/api/checklist/detail')) {
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

          await page.exposeFunction('printPdf', async () => {
            await page.pdf({
              path: fileName,
              format: 'A4',
              landscape: false,
              printBackground: true,
              displayHeaderFooter: true,
              headerTemplate: '<div style="font-size:10px; display: inline-flex;justify-content: center;width: 100%; padding: 0 20px;"></div>',
              footerTemplate: footerTemplateOrg,
              margin: {
                top: marginTop,
                bottom: marginBottom,
                right: marginRight,
                left: marginLeft,
              },
            });
            await page.close();
          });

          // 使Image加载完成
          await page.evaluate(async () => {
            console.log("window.innerHeight: ", window.innerHeight);
            window.scrollBy(0, window.innerHeight);
          });
          // const zoom = 0.8
          await page.evaluate(async function (zoom) {
            document.body.style.zoom = zoom;
            await window.printPdf();
          }, zoom);
        } finally {
          await browser.disconnect();
        }
      })();
    }
  });
});