const express = require('express');
const puppeteer = require('puppeteer');

const TIME_OUT = 60 * 1000;

var browser;

(async () => {
browser = await puppeteer.launch({
    executablePath: process.env.CHROME_BIN || null,
    args: [
        '--no-sandbox',
        '--disable-dev-shm-usage',
        '--no-first-run',
        '--no-zygote',
        '--disable-setuid-sandbox'
    ],
    timeout: TIME_OUT,
    // devtools: true,
    userDataDir: './puppeteer'
},);
})();

const app = express();

app.get('/', (req, res) => {
    res.send('Hello PrintPDF');
});

app.get('/generate_pdf', async (req, res) => {
    let param = req.query;
    let url = param["url"] || '';
    let path = param["path"] || '';
    let filename = param["filename"] || '';
    let cookie_name = param["cookie_name"] || '';
    let cookie_value = param["cookie_value"] || '';
    let cookie_domain = param["cookie_domain"] || '';
    if (path) {
        filename = path + '/' + filename
    }
    console.log(filename);
    if (!url || !path || !filename || !cookie_name || !cookie_value || !cookie_domain) {
        res.send({code: 100200, msg: "参数有误，请检查参数"});
        return;
    }
    ret = await generate_pdf(url, filename, cookie_name, cookie_value, cookie_domain);
    if (!ret) {
        return res.send({code: 100201, msg: "生成pdf失败"});
    } else {
        return res.send({code: 0, msg: "生成成功"});
    }
});

app.listen(9500);

async function generate_pdf(url, filename, cookie_name, cookie_value, cookie_domain)
{
    var page = null;
    var complete_flag = true;
    try {
        console.log(url);
        console.log(cookie_name, cookie_value, cookie_domain)
        page = await browser.newPage();
        // var cookies = await page.cookies(url);
        // console.log(cookies);

        await page.setCookie({
            name: cookie_name,
            value: cookie_value,
            domain: cookie_domain
        });
        page.on('response', async function (response) {
          if (response.url().includes('/api/checklist/detail')) {
            if (response.ok() && response.status() === 200) {
              let res = await response.text()
              res = JSON.parse(res)
              if (parseInt(res.error_code) !== 0) {
                // await page.close();
                // page = null;
                complete_flag = false;
                console.log(res);
              }
            } else {
              // await page.close();
              // page = null;
              complete_flag = false;
              console.log(response);
            }
          }
        })
        console.log("page.goto url: ", url);
        await page.goto(url, { waitUntil: 'networkidle0', timeout: TIME_OUT });

        let marginBottom = '24mm';
        const marginTop = '24mm',
          marginRight = '24mm',
          marginLeft = '24mm';

        let footerTemplateOrg = '' +
        '<div style="font-size:2mm;color:#394868;width:100%; display: inline-flex; align-items: center;' +
        'margin-left: 12mm;' +
        'margin-right: 15mm;' +
        '">' +
        '<div style="justify-content: flex-end; margin-left: auto; display: flex;">' +
        '<div class="pageNumber" style="display: block;"></div>/<div class="totalPages" style="display: block;"></div>' +
        '</div>' +
        '</div>';

        await page.exposeFunction('printPdf', async () => {
            await page.pdf({
              path: filename,
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
            console.log('print pdf');
          });

          // 使Image加载完成
          await page.evaluate(async () => {
            console.log("window.innerHeight: ", window.innerHeight);
            window.scrollBy(0, window.innerHeight);
          });
          const zoom = 1
          await page.evaluate(async function ({zoom, complete_flag}) {
            document.body.style.zoom = zoom;
            if (complete_flag) {
                await window.printPdf();
            }
          }, {zoom, complete_flag});
    } catch(e) {
        console.log(e);
        if (page) {
            await page.close();
            page = null;
        }
        return false;
    }
    if (page) {
        console.log('page is closed normally');
        await page.close();
        page = null;
    }

    if (!complete_flag) {
        return false;
    }
    return true;
}