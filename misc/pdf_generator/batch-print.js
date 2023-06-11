var webPage = require('webpage');
var system = require('system');
var page = webPage.create();
page.settings.userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36';

var type = system.args[1] || 'A4';
var cookie = system.args[2];
var url = system.args[3];
var path = system.args[4] || '';
var footer_text = decodeURI(system.args[5]) || '';
var check_id = system.args[6] || '';
var timeout = parseInt(system.args[7]) || 4000;
var star = url.indexOf('://') + 3
var end = url.indexOf('.com') + 4
var domain = url.substring(star, end)

phantom.addCookie({
  'name'     : 'fantastic',   /* required property */
  'value'    : cookie,  /* required property */
  'domain'   : domain,
  'path'     : '/',                /* required property */
  'httponly' : true,
  'secure'   : false,
  'expires'  : (new Date()).getTime() + (1000 * 60 * 60 * 3)   /* <-- expires in 1 hour */
});

var fileName = check_id + '-' + type + '.pdf';

// 设置a6窗口
if (type === 'a6' || type === 'A6') {
  page.viewportSize = {
    width: 280,
    height: 600
  };
}

page.onConsoleMessage = function(msg, lineNum, sourceId) {
  var date = new Date()
  console.log(date.getTime(), 'console')
  console.log(msg);
  if (msg === '图片加载完了') {
    window.setTimeout(function () {
      if (path) {
          fileName = path + '/' + fileName
      }
      page.render(fileName);
      phantom.exit();
    }, timeout);
  }
};
page.open(url, function() {
  // 设置大小
  if (type === 'a6' || type === 'A6') {
    page.paperSize = {
      format: 'A6',
      orientation: 'portrait',
      border: '0cm'
    }
  } else {
    page.paperSize = {
      format: 'A4',
      orientation: 'portrait',
      border: '0.8cm',
      footer: {
		height: "0.8cm",
		contents: phantom.callback(function(pageNum, numPages) {
          if (footer_text) {
          	return '<div style="font-size:8px;color:#394868;test-align:center;width:100%;"><span style="float:left;">' + footer_text + '</span>'+
                '<span style="display:inline-block;margin-left:160px;"><span style="color:#e74478;font-weight:500;">'+
                '慧心瞳 </span>&nbsp;&nbsp;报告咨询: 400-100-3999</span>' +
                '<span style="font-size:8px;float:right">第' + pageNum + '页</span>'+
                '</div>';
            // return "<div style='font-size:8px;color:#394868;'>" + footer_text + "<span style='font-size:8px;float:right'>第" + pageNum + "页</span></div>";
          } else {
            return "<div style='font-size:8px;color:#394868;'><span style='font-size:8px;float:right'></span></div>";
          }
		})
      }
    };
  }
  // 设置缩放
  var zoom ='0.56';
  page.evaluate(function(zoom) {
    document.body.style.zoom = zoom;
  }, zoom);
  var date = new Date()
  console.log(date.getTime(), 'print')
  // window.setTimeout(function () {
  //   if (path) {
  //     fileName = path + '/' + fileName
  //   }
  //   page.render(fileName);
  //   phantom.exit();
  // }, timeout);
});

