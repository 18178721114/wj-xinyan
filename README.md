# Airdoc对接方案介绍-福利码-V1.0

## 前置流程
Airdoc商务对接人申请开通账号和设备，并完成账号和设备的关系绑定

## 对接流程描述

在用户在第三方合作伙伴系统进行登记注册的情况下，可使用本文档中的API和Airdoc系统对接，完成用户健康扫描并获得检查报告。

1. 用户首先在合作伙伴系统中登记用户信息。
1. 合作伙伴系统通过接口一推送用户信息到Airdoc系统，同时获取福利码。（接口一 ）
1. 用户扫描Airdoc在设备上的二维码，收到消息，点击消息打开Airdoc小程序、输入福利码、用户协议授权、进入启动健康扫描仪页面、启动健康扫描仪。
1. 用户按照设备语音提示进行扫描，扫描结束后，健康扫描仪会自动上传扫描结果到Airdoc云端系统 。
1. 用户在合作伙伴系统查看报告
	* 通用方法
		* 参考接口二的描述提供接收报告推送的接口给Airdoc，Airdoc系统负责推送报告到合作伙伴系统的服务端
		* 合作伙伴服务端接收报告后，合作伙伴系统根据自身情况，可以在APP、小程序或公众号上提供用户查询报告的入口。
	* 直达小程序方法
		* Airdoc通过公众号推送模板消息给用户。
		* 用户点击模板消息直接跳转到小程序页面。参考下文微信直达小程序技术解决方案。
	* 无技术成本方法
		* 合作伙伴入口展示我们三方查询的链接，链接上的渠道号Airdoc会分配。比如：https://pe.airdoc.com/thirdparty/query?channel=100
		* 用户点击入口输入手机号和验证码查询报告。



## 微信直达小程序技术解决方案
Airdoc公众号上设置合作伙伴小程序为白名单小程序。
合作伙伴小程序设置Airdoc的域名为白名单，开发一个webview看H5报告的页面，参数为urlencode后的整个报告H5链接。


## 接口一：接收用户信息推送（Airdoc系统）

#### 场景和说明

* Airdoc系统使用本接口接收合作伙伴系统推送过来的⽤户信息。⼀次可传输多个⽤户的信息。

#### 接口地址
* 测试环境：https://staging-open.airdoc.com/api/openapi/receive\_base\_info
* 生产环境：https://pe-open.airdoc.com/api/openapi/receive\_base\_info

#### 请求方式
POST

HTTP HEAD： "Content-Type: application/json"

#### 参数说明（JSON）

| 字段名 | 类型 |是否必填 | 默认值 | 描述|
|---|---|---|---|---|
| appid |int| 是 | |  合作伙伴ID，Airdoc分配，在Airdoc系统内唯一|
| t |int| 是 | |  当前时间戳，参与签名 |
| user_id |int| 是 | |  账号ID，Airdoc分配 |
| salt | string|否 |  |  签名计算用，不传或传空不参与签名验证 |
| sign | string|是|  |  签名策略:  md5(appid+salt+secret\_key+t); appid，secret\_key：通过邮件提供加密文件，微信提供解密密钥的方式 |
| data[0].uuid   | string | 是 | | 合作伙伴系统中本次检查的唯一筛查号 |
| data[0].medical\_record\_no    | string | 是 | | 用户在合作伙伴系统的用户号，在合作伙伴系统全局唯一，最长45位字符串  ，比如openid |
| data[0].name    | string | 是 | | 姓名|
| data[0].gender    | int | 是 | |  1：男；2：女 |
| data[0].phone    | string | 是 | | 比如：13800000000 |
| data[0].birthday    | string | 是 | |  如：1990-02-02|
| data[0].medical_history | string | 否 | | 病史： 1：糖尿病；2：高血压；3：高血脂；4：肾病；5：风湿病；6：肿瘤；7：青光眼病史；8：白内障病史；多个用英文逗号分隔，比如："1,2"表示有糖尿病和高血压病史，没病史为0；|
| data[0].height    | int |是| |身高，单位厘米|
| data[0].weight    | float |是||体重，公斤|
| data[0].os_diopter    | int |是| 0| 左眼屈光度：-1: 不清楚；0：正常；75：近视50~99度；150: 近视100~199度；250: 近视200~299度；350: 近视300~399度；450: 近视400~499度；550: 近视500~599度；650: 近视600~699度；750: 近视700~199度；850: 近视800~899度；950: 近视超过900度；-75：远视50~99度；-150: 远视100~199度；-250: 远视200~299度；-350: 远视300~399度；-450: 远视400~499度；-550: 远视500~599度；-650: 远视600~699度；-750: 远视700~199度；-850: 远视800~899度；-950: 远视超过900度； |
| data[0].od_diopter    | int |是|0 |右眼屈光度，同上 |

#### 参数示例
~~~
{
    "appid": 870200,
    "t": 1527062055,
    "salt": "randstring",
    "user_id": "87621",
    "sign": "94a1b8563f08a8b9c65189161fb61922",
    "data": [
        {
			  "uuid": "9876567890",
            "medical_record_no": "22201805222033999",
            "name": "测试01",
            "gender": 1,
            "birthday": "1990-02-02",
            "medical_history": "1,2",
            "height": 170,
            "weight": 70,
            "phone": "13800000000",
			"os_diopter": 150,
			"od_diopter": 150
        }
    ]
}
~~~

#### 返回结果（JSON)

| 字段 | 必填  | 类型| 示例值 | 描述 |
|---|---|---|---|---|
| error_code | 是 | Int | 0 | 0:成功，100401:appid不存在，100402：签名验证失败|
| message | 是 | string | 0 | 比如：同步成功|
| data.verification_code | 是 | string | 0 | 6位长度的带字母的福利码|
| data.expire_in | 是 | string | 0 | 福利码的失效时间|

~~~
{
	error_code: 0,
	message: "同步成功",
	data: {
		verification_code: "",
		expire_in: "2020-12-10 00:00:00"
	}
}
~~~


## 接口二：接收Airdoc推送H5报告（合作伙伴系统）

#### 描述
合作伙伴系统中用来接收Airdoc系统推送过来的报告的接口，Airdoc会配置到自身系统中，并在报告生成后调用。接口是的基于http协议的 (支持https），请区分测试环境和生产环境。签名验证成功表示推送来源（Airdoc）可信任。

* 测试环境：TBD
* 生产环境：TBD

#### 请求方式
POST

HTTP HEAD： "Content-Type: application/json"

#### 参数示例（JSON）

~~~
{
	sign_type: "sha1",
	timestamp: 1603356013,
	signature: "4b41ad96b4e7e55b34f760b4490e977cb48065ae",
	h5: URL,
	sn: sn,
	created: "2021-05-07 12:00:00",
	uuid: "56789021211",
	medical_record_no: "2122121jkkjkj",
	verification_code: "654321"
}
~~~

#### 签名方法：
sha1(secret\_key + timestamp + verification_code)

#### 签名举例
secret\_key = "94a1b8563f08a8b9c65189161fb61aaa";  // 同接口一
timestamp = 1603356013;  // 来自于推送参数, 当前unix时间戳，单位到秒
verification\_code = "445678";
signature_string = "94a1b8563f08a8b9c65189161fb61aaa1603356013A87B21"
signature  = sha1(signature\_string)

#### 返回

~~~
{
	error_code: 0,
	message: "success"
}

{
	error_code: 1001,
	message: "签名验证失败"
}

{
	error_code: 1003,
	message: "其他情况的失败都归到1003"
}
~~~


## 开发指导

### 部署测试

1. 提交merge到merge_test分支
2. 