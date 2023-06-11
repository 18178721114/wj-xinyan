const puppeteer = require('puppeteer');
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

  let marginBottom = '12mm';
  const marginTop = '12mm',
    marginRight = '8mm',
    marginLeft = '8mm';

  // footerTemplate config
  // const footerTemplateOrg = '<div style="font-size:8px;display: inline-flex;justify-content: flex-end;width: 100%;padding: 0 20px;"> <div class="pageNumber"></div>/<div class="totalPages"></div></div>'
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
    let logoImg = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAH0AAAAZCAMAAAArO9FbAAABAlBMVEUAAADPA1zSA1nnGUfaDFPUBVj+9fjWB1baPYLld6bdSorVIW/dDlD++vzhYpngW5X98vf2y9zXLHbZOH/75u/bQoX1xdreUY7cRofUG2vyuNH30+PfVpHQB1/fEk7zvdXlF0nsl7vpi7TTFWf88fb86/LkbqDYMnv43Oj31uXzwNbxss3vq8niFkz//f764Ortn8Lngq774uzxrsnupcXnfarjap7iZpzSEWXRC2H97vP52uXohrHskbb+9/r30ODto8ToWIPmNWXjHVT0p7vwgZ3eIWDaE1joK1b2vM3raI/tZof5xNDvqMf2r8Hwj67gO3frSG3znrXshaniQ3vdK2w+9LsBAAAAAXRSTlMAQObYZgAABFlJREFUSMfF1Ody00AUBeBziREqUZcs2XLBknvvPSEJkITQ2/u/Crtr2SaZAX4whG/G2iuPPedKqyscjV9/vr16+w7/xbvbE+E1/oMBC9/5gsf39mTvdozH9u7k6A6PTP988hMbDzXP2mmVr+h4wItb+INOG/dlOzh69Vx4eSKWz/2HzTWo2NpVCbXxwJZM/IbFxEOL67NYG31Lki5iS7LS66jdPn/eu/poDzYfLnss/hXu6xJRHUKlvsYDMwrwS5Ihq34YBKoahuELbH25svEdOVRlR85DeN3r9d7W0tvwste7quIel3KUePdvh2h8M3lvs2svgBl3pm1rvxmHeqhWlaltZhUlm81W+7IXyZAWsSRNVQnChgVesvXFhzcSi2et3B/6LBXzGo3A9BcN3oVtOI0InrkiIj/g6ZKyZLU2BXP+Ux0rsdZ1cq62cl3XhmoNwpEha4uF4xgKuMtS6eUA53e9UunKZsPHTjf4iUkqFNIsvokJdYCzIhGV1w6LYB9i6ecqUc5hX8dIa9bYkO+UoagXTsN3E1n2XyBsZf1sPp/vdNihDOZNqVS6Y7efLSx+Lc4vcdRascTmii5YXc1RHhMiudJmTSXTqm67PH1ExbimtwpUjLDd1eaSp58pCGE6LN33/QiuL8vY8scgCOriPl2dnp5+QfX6VHgF7yVb3uCgTk4fLMvtp+kNCnS2X0Waij3OkalrNEuHY95PNwkeRPp5YM0cuZGT3YaN0ItCSW7VRkZt3BgA+MgzP2Bws0v/uuvjk45ULaEzSZLyRcqL9PZgN35d0s7BzckY0GoMEUZyi4pNHMQK8tpspDCV9lpXq81Qd4NCwykEmsc6vM5kMqcVSN8zwgTjm903qZhI44hUkZ61SeOP65QcCAoZG1ruRmZIrk0rDwezbT8MzMA1DMOtQVe9Frv2cqSYkc2v/U5kXrK2RfFNwkQU1+kEShrtFaNjupiEF+k8mtKSuqIuUEFKWH2wGFo+W9QIMCL0XdmV+74xlxvzhVNDNiPc2GwLrjM3l2PgW0aoQ7ig4rTMsaEzjulij2WPLQp/6kzSeCuVIk1gkGbz2+CXeWwboTGvq+p8XshDD+2yj0H3vaG+7/KGMs+ET1VgbfNB+/ps58YDz3ApOLzTlmP9mI52kXJzxSWevsnR0lBC4j9u5mglaoX9XW552e1oFG9H20oj4vOuRgXTdB3TLJTxneXs4sdpxrO9DpiIqIyd8YqGWPL0nEjHJEfMSuORtkOcafG3VlqvgVaQlX2VzVehYLBdgFxryWg2mzODHXQkT/d8HUzl6cFQbLsy6yM1NDz2npekegU7XsUwR1HTaLPaGi5MJW3UutjXkzkkzjZNw/Q9jHw5juv1uuywwwzOMS0CEx7P8/h761o6tpPJpLPm25VFu9vtdjrskIfyZK84AFM4nCdr/HPN5T5tDq58SD/DI+ik8aEFoUK78wUeRWTmksSJdaTKapIk7gUewQ/qa4RH7quN9gAAAABJRU5ErkJggg==" alt="" style="height:10px;margin-bottom:-2px;"/>';
    footerTemplateOrg = '<div style="width:100%; position:relative;padding:0 1%;box-sizing:border-box;">' +
      `<div style="color: #e7e8ec; margin: 0px 4mm 2px; font-size:2mm; padding-bottom: 3px; border-bottom: 1px solid #e7e8ec;">注：本报告仅供临床医生参考。</div>` +
      '<div style="font-size:2mm;color:#394868; display: flex; align-items: center;' +
      'margin-left: 4mm;' +
      'margin-right: 4mm;' +
      '">' +
      '<span style="justify-content: flex-start;  margin-right: auto">' + footer_text + '</span>' +
      '<div style="justify-content: center; margin: auto">' +
'<span style="color:#e74478;font-weight:500;">'+logoImg+'</span>&nbsp;&nbsp;' + telephone_text + '</div>' +
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
      format: 'A4',
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