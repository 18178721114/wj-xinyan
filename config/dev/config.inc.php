<?php
require_once(__DIR__ . '/../common_test/config.crypt_test.php');
define('PREFIX_CACHE', '');
define('IMG_DOMAIN_OSS_SAT', '');
define('ENV', 'test');
define('HUIXINTONG_FULL_NAME', '慧心瞳健康评估');
define('YINGTONG_FULL_NAME', '鹰瞳健康');
define('ZHONGYOU_FULL_NAME', '众佑健康评估');
define('SHORT_URL', 'http://test-s.airdoc.com');
define('EYE_DOMAIN', 'http://test-ikang.airdoc.com/');
define('BISHENG_DOMAIN', 'http://test-innerapi-bisheng.airdoc.com/');
define('FANGYUAN_DOMAIN', 'http://test-innerapi-fangyuan.airdoc.com');
define('EYE_DOMAIN_LOCAL', 'http://phantomjs.airdoc.com/');
define('EYE_DOMAIN_HTTPS_PE', 'http://test-ikang.airdoc.com/');
define('EYE_DOMAIN_HTTPS_OVERSEA', 'https://staging-oversea-hk.airdoc.com/');
define('IMG_DOMAIN', 'http://img.airdoc.com/');
define('IMG_DOMAIN_NEW', 'https://img6.airdoc.com/');
define('IMG10_DOMAIN_OSS', 'https://img10.airdoc.com/');
define('IMG10_DOMAIN_OSS_VPC', 'http://airdoc-image.oss-cn-beijing-internal.aliyuncs.com/');
define('IMG_SWITCH', 1);
define('LOGIN_LIMIT', 10);
#define('DR_MODEL_SERVICE', 'http://dr-api1.airdoc.com:8080/');
define('DR_MODEL_SERVICE', 'http://dr-api2.airdoc.com/');
define('FEEDBACK_DOMAIN', 'http://101.200.85.230/');
define('MODEL_SERVICE', 'http://172.17.10.103/');
define('MODEL_SERVICE_STAGING', 'http://test-model-api.airdoc.com/');
define('MODEL_RETINA_EX', 'http://172.17.67.254/');
define('ALGO_DISPATCH', 'http://10.1.3.52:9210/');
define('MODEL_RETINA_EX_STAGING', 'http://test-model-api.airdoc.com/');

// SME业务系统
define('SHEN_NONG_DOMAIN', 'http://10.1.3.15:8000');
define('SHEN_NONG_USERNAME', 'airdoc');
define('SHEN_NONG_PASSWORD', 'gU1KZPx27Y0sq6aw');

define('OVERSEA_ENCRYPT_KEY', '448b0fbe1a894050e9447ed79a87d9a4');

//172.17.10.103
define('EYE_PRICE1', 40); //单位元
define('EYE_PRICE2', 26); //单位元
define('AIRDOC_PRICE1', 22); //单位元
define('AIRDOC_PRICE2', 8); //单位元
define('EMAIL_HOST', 'smtp.office365.com');
define('EMAIL_USER_NAME', 'notify@airdoc.com');
define('EMAIL_PASSWORD', 'PDG@A1rd0c');
define('PA_CRYPT_KEY', '12345678');
define('UPDATE_DIAGNOSE_WHITE', [1, 1025]);
//李行微信 13521535660 - Airdoc人工智能
define('WX_APPID', 'wx74a5ff4bfc22d1f8');
define('WX_SECRET', 'd5215e028bb3dd2272e6fd897298c5c9');
// define('WX_APPID', 'wx5c17f5b7a6db9bd6');
// define('WX_SECRET', '1891604693716ee2e3254155a88fa672');
define('WX_REPORT_TEMPLATE_ID', 'sP2m8V-EIV-fzzHZBmFjcakVM7FBUUB-16opcJhICIk');
define('WX_BUY_SUCCESS_TEMPLATE_ID', 'SZHf4aoD3OaWvh0LtRrBoA34TignAHoqSHKEkcie1uA');
define('WX_SUBSCRIBE_ID', 'POZ4XUzmzmPU-IScJZ_FMYsk6oiDdY4Rkgmg0VdwAgs');
define('WX_SHOT_FAIL_ID', 'qsSKdHkJs2JKejuVLdfrDQ7dOXwUK92LBQDm1w5NN5Y');
define('WX_NIGHT_DELAY_NOTICE_TEMPLATE_ID', 'VAQRLthmsn2UcNHwFIBMdjvLCKCPdC0sPdnhiMVPgC8');

//李行微信 - 13126774448 （已丢失） 北京爱康
define('WX_APPID_NEW', 'wx3a60f4ff16dc8ac4');
define('WX_SECRET_NEW', 'ad8ae7e84783882f3016033967798e2c');
define('WX_REPORT_TEMPLATE_ID_NEW', 'B2HQWerOKe6pJPQtpPWdbAdLE_A18RMgdO7BrcKCJ_U');
define('WX_BUY_SUCCESS_TEMPLATE_ID_NEW', 'qljRGC4aqEOiSSNfk47PRLp7ZokiJWDdWBBB87QVJkk');
define('WX_SUBSCRIBE_ID_NEW', 'UMuYzUlK2jw-VpxYcSQqMd2OJKDtzH-TtIkAxRUAOLg');
define('WX_SHOT_FAIL_ID_NEW', 'qsSKdHkJs2JKejuVLdfrDQ7dOXwUK92LBQDm1w5NN5Y');
define('WX_SWITCH_TIME', '2018-12-04 18:00:00');

define('PRINT_APP_ID', 'fcb59834d8d1');
define('PRINT_APP_SECRET', '410eafd32de347afb8e2');
define('PRINT_EQUIPMENT_ID', '4748a34961a664a9');
define('PRINT_BASEURL', 'https://print.airdoc.com');
//平安医保科技
define('PA_APP_ID', 'PT100000004099');
define('PA_APP_SECRET', 'airdoc001secret');
define('PA_DOMAIN', 'https://test1-city.pingan.com.cn/');


define('UPGRADE_B_FEE', '1');
define('UPGRADE_C_FEE', '2');


define('AK_API_DOMAIN', 'https://uat-reportapi.health.ikang.com/');
define('AK_H5_DOMAIN', 'https://uat-report.health.ikang.com/');
define('TJB_H5_DOMAIN', 'https://uat-report.tijianbao.com/');

define('ROMA_DOMAIN', 'https://test-innerapi-roma.airdoc.com');
// define('ROMA_DOMAIN', 'http://10.100.2.71:8540');

// SMB
define('CAMERA_IOT_DOMAIN', 'http://fd16-iot.airdoc.com/');
define('FD16_ORG_ID', 40067);

// ICVD test account
define('ICVD_WX_APPID', 'wx6dd1f9faa2745d54');
define('ICVD_WX_OPENID_PREFIX', 'oav-Y');
define('ICVD_WX_SECRET', '98a9eb3bccd9cbeb66004651a907bace');
define('ICVD_REPORT_NOTICE_TEMPLATE_ID', 't5A2Emafz1hDmUyYb_jMrQ6JAGHTDRDYpP9NmSGt_Nc');
define('ICVD_HEALTH_ADVICE_TEMPLATE_ID', 'WPPN9NkD1Qixp-2g3Z9DCUH9zlGxrVsvJApXeG0dnHI');
define('ICVD_ORG_ID', 40073);
define('PA_HFL_ORG_ID', [41637]);
define('ICVD_ORG_ID_2', 40485);
define('ICVD_PCODE_PREFIX', '8997');

define('ZX_PCODE_PREFIX', '8981');

// 众佑 - 李行 18611367586 gongzhonghao@airdoc.com A123
define('ZY_WX_APPID', 'wx0c981ce69c027ebf');
define('ZY_WX_TOKEN', 'd7174cbb441a0439d0693c729b033bec');
define('ZY_WX_OPENID_PREFIX', 'oVqmF');
define('ZY_WX_SECRET', 'd9d01c9ca1bae9434a4cb4ef00eaa798');

define('ZY_WX_SUBSCRIBE_ID', 'U4mAwwQB5sTZ2fOjSOwJ_WY4tmOwvNvmkmFkl-g5GLE');
define('ZY_WX_SHOT_FAIL_ID', 'xhaiPdwYZYdCh8bhf-mvLP4b5DnCLLMOhvT0v_Vg8hg');
define('ZY_REPORT_NOTICE_TEMPLATE_ID', 'AG6eSXyWVMlezWmh4bm9S104JGBfy1SJHtGldGICk1o');
define('ZY_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', 'UAf0fWnhkAgZYdSDmFQXcVzgRmeu6fc4v5U68kbxp9U');
define('ZY_REGISTER_TEMPLATE_ID', '');

define('INTERNAL_SECRET', 'AirdocCTWFUS001');
define('CHUNYU_ORG_ID', 40075);
define('TAIPING_ORG_ID', 40087);
define('PHARMACY_ORG_ID', 40088);
//voice
define('ICVD_VOICE', 'Xn_KfJw8FHiWUC3KevbTA892Y59II2nuIxbmHRztT14');

// 西藏
define('TIBET_ORG_ID', 40197);

define('PA_ORG_ID', 40124);
define('PA_JZ_ORG_ID', 40271);
define('PA_ZY_ORG_ID', 40272);
define('PA_APP_ORG_ID', 40409);
define('PA_ALL_ID', [PA_APP_ORG_ID, PA_ZY_ORG_ID, PA_JZ_ORG_ID, PA_ORG_ID]);

// CMS
define('CMS_DOMAIN', 'http://test-cms.airdoc.com/');

// RISK
define('RISK_TIME', '2020-05-12 00:00:00');
define('YT_RISK_TIME', '2020-05-23 00:00:00');
define('DBHT_RISK_TIME', '2020-08-11 00:00:00');
define('USABLE_DISEASES_TIME', '2020-08-21 00:00:00');
// 算法接管风险
define('RISK_V2_TIME', '2020-11-07 00:00:00');
define('COMPREHENSIVE_RISK_ORG_ID', [1]);

define('RISK_SMOKE_TIME', '2020-11-25 00:00:00');
define('MPOD_DID_TIME', '2022-09-21 12:00:00');

define('HISTORY_INTERVAL', 1);

// AGENT
define('AGENT_TOOL_DOMAIN', 'https://dev-agent-tool.airdoc.com/');


define('ANEMIA_ORG_ID', 40128);

// new_template
define('NEW_TEMPLATE_0', 'print/'); // 慧心瞳
define('NEW_TEMPLATE_1', 'print/pdf1/');
define('NEW_TEMPLATE_2', 'print/pdf2/');
define('NEW_TEMPLATE_2_1', 'print/pdf2-1/');
define('NEW_TEMPLATE_3', 'print/ytpdf/');
define('NEW_TEMPLATE_4', 'print/pdf301/');
define('NEW_TEMPLATE_6', 'pc-v2/print-public/'); // 公立体检
define('NEW_TEMPLATE_7', 'pc-v2/visualCheckPDF?en_check_id=');
define('NEW_TEMPLATE_9', 'print/zyPdf/');
define('NEW_TEMPLATE_10', 'print/tzjPdfC3m1/');
define('NEW_TEMPLATE_11', 'print/tzjPdf/');
define('NEW_TEMPLATE_12', 'pc-v2/print-professional/'); //眼科
define('NEW_TEMPLATE_13', 'pc-v2/print-professionalA/'); // 眼科报告A版
define('NEW_TEMPLATE_14', 'pc-v2/print-youngsters/'); // 青少年近视防控
define('NEW_TEMPLATE_15', 'pc-v2/print-ytmedical/'); // 鹰瞳医疗
define('NEW_TEMPLATE_16', 'pc-v2/ytHeathyPdf?en_check_id='); //鹰瞳健康
define('NEW_TEMPLATE_17', 'pc-v2/print-ytmedical/'); // 鹰瞳医疗经典版
define('NEW_TEMPLATE_18', 'pc-v2/print-ytmedical/'); // 海外鹰瞳医疗
define('NEW_TEMPLATE_19', 'pc-v2/ythPdf/index?en_check_id='); //鹰瞳健康2.0
define('NEW_TEMPLATE_20', 'pc-v2/print-ytmedical/'); // 鹰瞳KA/SME

define('NEW_TEMPLATE_MEDICAL_HEATHY', 'pc-v2/ytHeathyPdfPE?en_check_id='); // 鹰瞳医疗健康风险
define('NEW_TEMPLATE_OVERSEA_MEDICAL_HEATHY', 'pc-v2/ytHeathyPdfPE?en_check_id='); // 海外鹰瞳医疗健康风险

// 筛查登记小程序
define('REGISTER_WX_APPID', 'wxe15965084f03c697');
define('REGISTER_WX_SECRET', 'ddc73424a0743638505e472643b43b29');
// SAT小程序
define('SAT_WX_APPID', 'wx9edb755c16cad8eb');
define('SAT_WX_SECRET', '3210be713b5ee3a63ed475479cf89107');
// FD16设备控制小程序
define('FD16_WX_APPID', 'wxd8cdabdc2c7f432a');

// 护心宝 - 李兴龙
define('DCG_WX_APPID', 'wx719baf0863535626');
define('DCG_WX_TOKEN', '7bc51b94e33e6f7171d9937dd2d9a49e');
define('DCG_WX_OPENID_PREFIX', '');
define('DCG_WX_SECRET', '7469668a2b3b2bbee479f43ff479a9d5');
define('DCG_WX_SUBSCRIBE_ID', '');
define('DCG_REPORT_NOTICE_TEMPLATE_ID', '');
define('DCG_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', '');
//体之健客户ids
define('TZJ_COSTOMER_IDS', [23, 24, 25]);
//护心宝
define('DCG_PCODE_PREFIX', '6000');
// Hospital
define('HOSPITAL_CUSTOMER_IDS', [8, 11, 14]);
//护心宝 -小程序
define('DCG_APPLETS_APPID', 'wx47605de3c36e829c');
define('DCG_APPLETS_SECRET', 'dfda7098cd1ddd44ce467c5be29bce1d');

//复兴康养
define('FUXINGKANGYANG_ORG_ID', 40394);
// 体知健
define('TZJ_WX_APPID', 'wx2d63529153a03543');
define('TZJ_WX_TOKEN', '47e5fc368c334feb9176a0e56cddae79');
define('TZJ_WX_OPENID_PREFIX', 'oZ45S');
define('TZJ_WX_SECRET', '5db6612538fe4bfc985518edacc54e47');
define('TZJ_WX_REGISTER_TEMPLATE_ID', 'tXHgyEufTHTleSTKupbqX4fWLsGC-mrOcyJ8vv5culU');
define('TZJ_FULL_NAME', '体知健健康评估');
define('TZJ_REPORT_NOTICE_TEMPLATE_ID', 'FXURpiJ-t-vCdPIv9R96NJ3Uii0fBwNRAu0OHZbxG7o');
define('TZJ_WX_SUBSCRIBE_ID', '674cGcoQ75TnIgv6rMjdl3jQkCi9Tg9RwNZ7RRFjpIY');
define('TZJ_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', 'i75bc8ghdzC9nX9waM2iZYHYaTNvDjYL2XIUqfTxcjU');
define('TZJ_WX_HEALTH_WARNING', 'lqxCSLvEYPamtooB7Uq6hmsmFXam9lmd7lM9GBXkJBI');
define('TZJ_WX_SHOT_FAIL_ID', 'rJI02e1Bq9ocP8G_LVIsuquyXSeDBvutBbZTMMW0I44');

// 鹰瞳健康
define('YTHEALTH_WX_APPID', 'wxf79b453eb04c8d9b');
define('YTHEALTH_WX_TOKEN', '1b0381d13b01674b97fb5a2e47849b2b');
define('YTHEALTH_WX_OPENID_PREFIX', 'oZLtb');
define('YTHEALTH_WX_SECRET', '7c9ac7c6a441b2bee2b8f89a353d7f09');
define('YTHEALTH_WX_REGISTER_TEMPLATE_ID', 'y4kvHnvEpqB4_qBpMq50WPHqSE3qphWyjShbDEVJT8M');
define('YTHEALTH_FULL_NAME', '鹰瞳健康');
define('YTHEALTH_REPORT_NOTICE_TEMPLATE_ID', '0HykgHBMWZFrkiApz5clppTpgo8T5rQ7x5Y-65HPe7M');
define('YTHEALTH_WX_SUBSCRIBE_ID', 'bTZ7jmHjxtCGhPf5GSy-KQUBVsgDWOmxqFkEtXiY-oY');
define('YTHEALTH_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', '5XDVnKn2Upu0WneVAxCBp1VhYddmzfVYpQFPYz7dCG8');
define('YTHEALTH_WX_HEALTH_WARNING', 'fZ2o6_nj_Y14I0UW0_rgPyfQYaUyxKri06sQjElnV-g');
define('YTHEALTH_WX_SHOT_FAIL_ID', 'nZw5V_WDAscp4JMmE4TNim6O2r0VbWF87w4yprHXUs0');
define('YTHEALTH_WX_HEALTH_MANAGEMENT_REMIND_ID', 'q_xH3Pfei2xTGx9iPbPhqYfmnV_M4mPd4lFa4kIZ1xk');


//FD16-admin
define('FD16_ADMIN_URl', 'https://test-fd16-admin.airdoc.com');
//导流机构
define('DISTRIBUTION_ORG_ID', 40597);
//华泰
define('HUATAI_ORD_ID', 40522);
//叶开泰
define('YEKIATAI_ORG_ID', 40527);

//常用联系人电话
define('PHONE_QINYONG', 18811060560); //秦勇
define('PHONE_XUYANHUA', 18600419157); //徐彦华 Emma
define('PHONE_LIHANG', 13521535660); //李行
define('PHONE_ZHANGSHILONG', 13488738742); //张世龙
define('PHONE_CHENFEI', 13910416240); //陈飞
define('PHONE_YAOGUANGYUAN', 1381777792); //姚光远
define('PHONE_DEKAI', 17319270713); //得凯
define('PHONE_ZHANGBING', 18810567213); //张兵
define('PHONE_CHENHAILONG', 13811885439); //陈海龙
define('PHONE_ZHANGLAN', 15116991725); //张岚
define('PHONE_WENGJING', 15901060491); //翁劲
define('PHONE_LIUSONG', 18610838951); //刘松

//常用部门ID
define('DEPARTMENT_PU', 1066); //一部PU 1066：陈飞 13910416240  已弃用
define('DEPARTMENT_MU', 1068); //二部MU 1068 ：秦勇 18811060560 已弃用
define('DEPARTMENT_RU', 1065); //三部RU 1065：姚光远 1381777792  已弃用
define('DEPARTMENT_SME', 1142); //SME 1142 ：秦勇 18811060560
define('DEPARTMENT_KA', 1141); //KA 1141 ：姚光远 1381777792
define('DEPARTMENT_MPC', 1144); //MPC（欢曈） 1144 ：陈飞 13910416240
define('DEPARTMENT_OBU', 1145); //OBU（海外） 1145 ：张岚 15116991725


//微信第三方开放平台
define('COMPONENT_APPID', 'wx4468a8669d2c2ef7');
define('COMPONENT_APPSECRET', 'cf93644e2b00e6672d255291c6cf9b5d');
define('COMPONENT_ENCODING_AESKEY', 'a3f9fdsutn5DjfdsieD04FJsdknvbfdF3bAmkpqs2cW');
define('COMPONENT_TOKEN', 'AIRDOCGDZ');

//鹰瞳收费宝接口地址
define('ADC_PAY_URL', 'https://openapi-adc.airdoc.com');

//公钥
define('PUBLIC_KEY', 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC1RaV8f3AsKTBRttJ/iRAqE3fD
T4wdfrgGtBEfrNzRxzi3vf0WDL6/LxSjubwwZGkQ2iSaAod07RUUIkdO5f0L2wcC
t8FMq9N4Ey33RTeOmUIXQtveVBWxX9D8/gK06q2jWOJyGOHbpJmhYfHRy9YCqrIm
L1K+zWtAMO+vdvCM7QIDAQAB');
//秘钥
define('PRIVATE_KEY', 'MIICXAIBAAKBgQC1RaV8f3AsKTBRttJ/iRAqE3fDT4wdfrgGtBEfrNzRxzi3vf0W
DL6/LxSjubwwZGkQ2iSaAod07RUUIkdO5f0L2wcCt8FMq9N4Ey33RTeOmUIXQtve
VBWxX9D8/gK06q2jWOJyGOHbpJmhYfHRy9YCqrImL1K+zWtAMO+vdvCM7QIDAQAB
AoGAFuWDL6SRMKLLPacQE5fmeMoYuIzVr+wPppkcCJo3EjBN07elviFB/rgdrUiK
orosIzrKoMFtBrHjlbV2uFqIib5kWnDs7q5zg8FWQ0upo6SvzTWqbTURa4LJH2cz
tFdfF1CxPfdVx2EAcVwHf4CzCaNjhnbn0zn9mw3qgIsig4ECQQDbwMO8lyNLsBdt
rj/CsJTP64ZMCTQ/wz1x3LetMfJMGrhENNR6LsP3qCkv7jbudAzZh3ARW57e2BZ4
Bo7B3vKxAkEA0yv+3T+S+UZBQwgzpYF+EzR9X7uhAMIDeoVtsbOE2/KEqajFXz+o
MkyAp4D9olxCb9b4WIdrFhJbhuMdMWr0/QJBAKUSBrZKXaQEMYUdKB4J4K7Sf73s
CAiBk01Ne9eothY+1/28JYNmT6Rf+Bhd+3thRym72A3h4dQJQ8+DNYch/vECQGuo
znL16nRzwOnv0ITck+4uoIyiF99PCn74b4hdQarw5XmptZZt2c5q+lxrguO3rZdf
PZXE1G+YRFlsiIGdSfECQAXVNu8My0jWfSNJyoCy3i4uYwhsP/1CFIZdPiSPEjbQ
EaUoJmuCTE1j/cakctfqhOHyajF8MAwq9Mot5rn7euU=');
