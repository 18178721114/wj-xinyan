const puppeteer = require('puppeteer');
const fs = require('fs');
const configObj = require('./config');
const os = require('os');

(async () => {
  function getArgvObj() {
    let i = 2;
    const obj = {}
    for (let i = 2; i < process.argv.length; i++) {
      obj[process.argv[i].split('=')[0]] = decodeURIComponent(process.argv[i].split('=')[1])
    }
    return obj
  }
  const argvObj = getArgvObj()
  console.log(argvObj)
  console.log(os.platform())
  let browser = '';
  //if (os.platform() === 'darwin') {
  //  browser = await puppeteer.launch(); // mac用
  //}  else {
    browser = await puppeteer.launch({ // 服务器上用
      executablePath: '/usr/bin/chromium-browser'
    })
  //}
  const version = await browser.version();
  console.log('版本是',version )
  const page = await browser.newPage();
   await page.setViewport({
   width: 1920,
   height: 1080
  })
  await page.setCookie({
      name: argvObj.cookieName || configObj.cookieName,
      value: argvObj.cookieValue || configObj.cookieValue,
      domain: argvObj.cookieDomain || configObj.cookieDomain,
      expires: Date.now() + 1000000
    }
  )
  await page._client.send('Page.setDownloadBehavior', {
    behavior: 'allow', 
    downloadPath: argvObj.savePath || configObj.savePath
  })
  await page.goto(argvObj.origin || configObj.origin);
  await page.evaluate(() => {
    localStorage.setItem('isLogin', true)
    return Promise.resolve('')
  })
  await page.goto(argvObj.ppturl || configObj.ppturl);
  await page.screenshot({ path: 'example.png' });
  page.pdf({path: 'example.pdf'})
  const showTitle = await page.evaluate(() => {
    let list = document.querySelectorAll('input');
    let title = ''
    for (let i = 0; i < list.length; i++) {
      if (title.length) {
        title = title + '_' + list[i].value
      } else {
        title += list[i].value 
      } 
    }
    return Promise.resolve(title)
  })

  function waitForFile(fileName, times){
    return new Promise(function(resolve, reject){
      let timeout = 1000
      if (!times) times = 0;
      times += 1;
      let timer = setTimeout(function() {
        fs.access(fileName, fs.constants.R_OK, (err)=>{
          if(!err){
            browser.close();
            console.log(`文件 ${fileName} 已出现.`)
            if (argvObj.fileName) {
              fs.rename(fileName, (argvObj.savePath || configObj.savePath) + argvObj.fileName +'.pptx', (res) => {
                console.log(res)
              } )
            }
            resolve(`文件 ${fileName} 已出现.`)
          }else{
            if (times > 120) {
              browser.close();
              return
            }
            waitForFile(fileName, times)
            console.log(`文件 ${fileName} 未找到.`)
            resolve(`文件 ${fileName} 未找到.`)
          }
        })                
      }, timeout)
  
      fs.access(fileName, fs.constants.R_OK, (err)=>{
        if(!err){
          browser.close();
          clearTimeout(timer)
          if (argvObj.fileName) {
            fs.rename(fileName, (argvObj.savePath || configObj.savePath) + argvObj.fileName +'.pptx', (res) => {
              console.log(res)
            } )
          }
          resolve(`文件 ${fileName} 已存在.`)
        }
      })
    })
  }
  waitForFile((argvObj.savePath || configObj.savePath) + showTitle + '.pptx')
})();
