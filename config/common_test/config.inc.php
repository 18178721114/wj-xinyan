<?php
require_once(__DIR__ . '/config_special.inc.php');
require_once(__DIR__ . '/config.crypt_test.php');
define('ENV', 'test');
define('PREFIX_CACHE', '');
define('HUIXINTONG_FULL_NAME', '慧心瞳健康评估');
define('YINGTONG_FULL_NAME', '鹰瞳健康');
define('ZHONGYOU_FULL_NAME', '众佑健康评估');
define('EYE_DOMAIN', 'https://' . SECOND_DOMAIN . '.airdoc.com/');
define('EYE_DOMAIN_LOCAL', EYE_DOMAIN);
define('IMG_DOMAIN', 'http://img.airdoc.com/');
define('BISHENG_DOMAIN', 'http://test-innerapi-bisheng.airdoc.com/');
define('FANGYUAN_DOMAIN', 'http://test-innerapi-fangyuan.airdoc.com');
define('IMG_DOMAIN_HTTPS', 'https://img3.airdoc.com/');
define('EYE_DOMAIN_HTTPS_OVERSEA', 'https://staging-oversea-hk.airdoc.com/');
define('EYE_DOMAIN_HTTPS', 'http://' . SECOND_DOMAIN . '.airdoc.com/');
define('EYE_DOMAIN_HTTPS_PE', 'https://' . SECOND_DOMAIN . '.airdoc.com/');
define('IMG_DOMAIN_NEW', 'http://img6.airdoc.com/');
define('IMG_DOMAIN_NEW_HTTPS', 'https://img6.airdoc.com/');
define('SAAS_HTTPS', 'https://test-saas.airdoc.com/');
define('IMG_SWITCH', 1);
//用户登录名称 oss@1341138187371998.onaliyun.com
define('OSS_BUCKET', 'adc-fundus');
define('OSS_ENDPOINT', 'oss-cn-beijing.aliyuncs.com');
define('OSS_ENDPOINT_SHANGHAI', 'oss-cn-shanghai.aliyuncs.com');
define('IMG_SWITCH_OSS', 1);
define('IMG_DOMAIN_OSS', 'https://img8.airdoc.com/');
define('IMG_DOMAIN_OSS_VPC', 'http://adc-fundus.oss-cn-beijing-internal.aliyuncs.com/');
define('IMG10_DOMAIN_OSS', 'https://img10.airdoc.com/');
define('IMG10_DOMAIN_OSS_VPC', 'http://airdoc-image.oss-cn-beijing-internal.aliyuncs.com/');
define('IMG_DOMAIN_OSS_SAT', 'https://airdoc-sat.oss-cn-shanghai.aliyuncs.com/');
define('IMG_DOMAIN_OSS_SAT_VPC', 'http://oss-cn-shanghai-internal.aliyuncs.com/');
define('ALGO_DISPATCH', 'http://test-algo-dispatch.airdoc.com/');

define('OVERSEA_ENCRYPT_KEY', '448b0fbe1a894050e9447ed79a87d9a4');
// SME业务系统
define('SHEN_NONG_DOMAIN', 'https://staging-aisp.airdoc.com/api');
define('SHEN_NONG_USERNAME', 'airdoc');
define('SHEN_NONG_PASSWORD', 'gU1KZPx27Y0sq6aw');

define('MAX_CRYPT_CHECK_ID', 2598342);
define('PK_ORG_IDS', [40395]);
define('AGENT_VIP_ORG_IDS', [40692, 40661]);

define('LOGIN_LIMIT', 20);
define('MODEL_SERVICE', 'http://172.17.67.255/');
define('MODEL_SERVICE_STAGING', 'http://172.17.10.103/');
#define('MODEL_RETINA_EX', 'http://172.17.67.254/');
define('MODEL_RETINA_EX', 'http://172.17.67.255/');
define('MODEL_RETINA_EX_STAGING', 'http://test-model-api.airdoc.com/');
define('DP_NIGHT', '23:00:00');
define('DP_MORNING', '07:00:00');
define('FEEDBACK_DOMAIN', 'http://feedback.airdoc/');

define('AK_API_DOMAIN', 'https://uat-reportapi.health.ikang.com/');
define('AK_H5_DOMAIN', 'https://uat-report.health.ikang.com/');
define('TJB_H5_DOMAIN', 'https://uat-report.tijianbao.com/');

define('EMAIL_HOST', 'smtp.office365.com');
define('EMAIL_USER_NAME', 'report@airdoc.com');
define('PA_CRYPT_KEY', '12345678');
define('UPDATE_DIAGNOSE_WHITE', [1]);
//李行微信 13521535660 - Airdoc人工智能
// 见config_special.inc.php


define('RI_ORG_IDS', [43119]);

define('ZY_PCODE_PREFIX', '8996');

define('SKB_ORG_ID', 40625);
define('SKB_ORG_ID_YT', 40626);
// ICVD test account 郭月帅微信
// 见config_special.inc.php
define('ICVD_ORG_ID', 40073);
define('ICVD_ORG_ID_2', 40485);
define('PA_HFL_ORG_ID', [41637, 42143]);
define('ICVD_PCODE_PREFIX', '8997');
define('ICVD_ANLYZE', 1);

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

// SAFE_HOUSE_DOMAIN
define('SAFE_HOUSE_DOMAIN', 'https://test-safe-house.airdoc.com/');

// SMB
define('CAMERA_IOT_DOMAIN', 'http://fd16-iot.airdoc.com/');
define('FD16_ORG_ID', 40067);
define('FD16_ORG_ID_ARY', [40070]);

define('YANGGUANG_APPKEY', 'aceb006676f04ca8a7d244287960c4b4');

define('ZX_PCODE_PREFIX', '8981');

define('ZY_CUSTOMER_IDS', [15, 17]);

define('INTERNAL_SECRET', 'AirdocCTWFUS001');
define('CHUNYU_ORG_ID', 40075);
define('TAIPING_ORG_ID', 40087);
define('PHARMACY_ORG_ID', 40088);

// 西藏
define('TIBET_ORG_ID', 40197);

// RISK
define('RISK_TIME', '2020-05-12 00:00:00');
define('YT_RISK_TIME', '2020-05-23 00:00:00');
define('SUMMARY_RISK_TIME', '2020-07-01 00:00:00');
define('FD16_BAOWEN_TIME', '2020-07-20 00:00:00');
define('BAOWEN_V2_TIME', '2020-08-21 21:00:00');
define('BAOWEN_V3_TIME', '2020-09-02 20:00:00');
define('DBHT_RISK_TIME', '2020-08-11 00:00:00');
define('USABLE_DISEASES_TIME', '2020-08-21 00:00:00');
define('DOUBLE_BAD_V2_TIME', '2020-08-28 16:00:00');
define('DID_CONSISTENCY_TIME', '2021-02-04 22:00:00');
define('MPOD_DID_TIME', '2022-09-21 12:00:00');

define('HISTORY_INTERVAL', 1);
// 算法接管风险
define('RISK_V2_TIME', '2020-11-07 00:00:00');
define('COMPREHENSIVE_RISK_ORG_ID', [1]);

define('RISK_SMOKE_TIME', '2020-11-25 00:00:00');

// AGENT
define('AGENT_TOOL_DOMAIN', 'https://dev-agent-tool.airdoc.com/');
define('PA_ORG_ID', 40124);
define('PA_JZ_ORG_ID', 40271);
define('PA_ZY_ORG_ID', 40272);
define('PA_APP_ORG_ID', 40409);
define('PA_ALL_ID', [PA_APP_ORG_ID, PA_ZY_ORG_ID, PA_JZ_ORG_ID, PA_ORG_ID]);

define('TCBJ_ORG_IDS', 'tcbj_org_ids');

// 福利码模式和扫码启动，只能使用两个同时切换。因为平安健康险APP这一个入口
define('PA_SWITCH', 1); // 0:普通模式，1:注册模式，2:福利码模式，3.扫码启动
define('PA_APP_SWITCH', 0); // 0:普通模式，1:注册模式，2:福利码模式，3.扫码启动

// 众佑平安广告
define('ENCRYPT_KEY_ZY_PA_ADS', '19AhZYEmKLCuTvqX');

define('ANEMIA_ORG_ID', 40128);
define('HELIAN_ORG_ID', 40161);

// 九州速要
define('JZSY_ORG_ID', 40188);
// 宝石花
define('BSH_ORG_ID', 40174);
// new_template
define('NEW_TEMPLATE_0', 'print/');
define('NEW_TEMPLATE_1', 'print/pdf1/');
define('NEW_TEMPLATE_2', 'print/pdf2/');
define('NEW_TEMPLATE_2_1', 'print/pdf2-1/');
define('NEW_TEMPLATE_3', 'print/ytpdf/');
define('NEW_TEMPLATE_4', 'print/pdf301/');
define('NEW_TEMPLATE_6', 'pc-v2/print-public/');
define('NEW_TEMPLATE_8', 'pc-v2/print-cantian/');
define('NEW_TEMPLATE_7', 'pc-v2/visualCheckPDF?en_check_id=');
define('NEW_TEMPLATE_9', 'print/zyPdf/');
define('NEW_TEMPLATE_10', 'print/tzjPdfC3m1/');
define('NEW_TEMPLATE_11', 'print/tzjPdf/');
define('NEW_TEMPLATE_12', 'pc-v2/print-professional/');
define('NEW_TEMPLATE_13', 'pc-v2/print-professionalA/'); // 眼科报告A版
define('NEW_TEMPLATE_14', 'pc-v2/print-youngsters/'); // 青少年近视防控
define('NEW_TEMPLATE_15', 'pc-v2/print-ytmedical/'); // 鹰瞳医疗专业版
define('NEW_TEMPLATE_16', 'pc-v2/ytHeathyPdf?en_check_id='); //鹰瞳健康
define('NEW_TEMPLATE_17', 'pc-v2/print-ytmedical/'); // 鹰瞳医疗经典版
define('NEW_TEMPLATE_18', 'pc-v2/print-ytmedical/'); // 海外鹰瞳医疗
define('NEW_TEMPLATE_19', 'pc-v2/ythPdf/index?en_check_id='); //鹰瞳健康2.0
define('NEW_TEMPLATE_20', 'pc-v2/print-ytmedical/'); // 鹰瞳KA/SME

define('NEW_TEMPLATE_MEDICAL_HEATHY', 'pc-v2/ytHeathyPdfPE?en_check_id='); // 鹰瞳医疗健康风险
define('NEW_TEMPLATE_OVERSEA_MEDICAL_HEATHY', 'pc-v2/ytHeathyPdfPE?en_check_id='); // 海外鹰瞳医疗健康风险

define('PUHUIBAO_ORG_IDS', ['42199']); // 普惠保

// 好数科技
define('HAOSHU_APP_ID', '12006');
define('HAOSHU_APP_KEY', '7abb7e0e422a32e');
define('HAOSHU_APP_SECRET', 'f37c061db6bfa747d91ccdef07d4a4f8');
define('HAOSHU_CALLBACK_URL', 'https://test-ikang.airdoc.com/api/fd16/receiveAuthCode');

//Roma
define('ROMA_DOMAIN', 'https://test-innerapi-roma.airdoc.com');

// 中信保诚
define('CITIC_ORG_ID', 40101);
define('CITIC_API_HOST', 'https://test2.citicpruagents.com.cn/aihome/');
define('CITIC_APPID', '14521231');
define('CITIC_SECRET', '4QK6I0oQEwB0Ju0g3v3uR9sMYtmH2MAq');
define('CITIC_KEY', '75cd163e784c41d5ad972add05524c0e');
define('CITIC_QUERY_REPORT_URL', 'http://testtijian.52190.com/query.html');

// CMS
define('CMS_DOMAIN', 'http://test-cms.airdoc.com/');
// define('CMS_DOMAIN', 'https://cms-admin.airdoc.com/');
define('ADS_SWITCH', 1);

// 运营工具
define('CV_DOMAIN', 'https://test-cv-admin.airdoc.com/');

//STI
define('STI_ORG_ID', 40213);
//define('STI_SYNC_URL', 'http://k8s-alpha.youlanai.cn/v1/device/airdoc/setReport');
define('STI_SYNC_URL', 'https://api-sit.sti-medical.cn/common-api/airdoc/setReport');

// 筛查登记小程序
define('REGISTER_WX_APPID', 'wx321a5e5800211fd0');
define('REGISTER_WX_SECRET', '9a5504d27534862b6a39444524d4d1de');

// SAT小程序
define('SAT_WX_APPID', 'wx9edb755c16cad8eb');
define('SAT_WX_SECRET', '3210be713b5ee3a63ed475479cf89107');

define('SINGLE_BAD_TIME', '2020-10-28 20:00:00');

define('FD16_WX_APPID', 'wxd8cdabdc2c7f432a');

// Army
define('ARMY_ORG_ID', ['40251']);
define('VCODE_ORG_ID', [40258, 40337, 40410]);
define('TAIBAO_ORG_ID', [40258]);
define('TAIKANG_ORG_ID', [40337, 40410]);
define('TAIKANG_HXT_ORG_ID', [40794]);
define('TAIKANG_ZY_ORG_ID', [40410]);
define('TAIKANG_YTH_ORG_ID', [41954]);
define('YUANMENG_2023', [43049]);
define('HK_KYB_ORG_IDS', ['42234']); //香港快验保

// 合谐医疗 SME-35 已支持在盘古机构配置中修改价格，其余机构价格在此配置
define('SALESMAN_PRICE_DEFAULT', 398); //业务员登记页面默认的支付价格
define('SALESMAN_ORIGIN_PRICE_DEFAULT', 998); //业务员登记页面默认的原价

// SME-80 江西新华 风险分组
define('JIANGXI_XINHUA_ORG_IDS', [43032]);

// 诺和诺德自定义问卷 SME-67
define('NOVONORDISK_ORG_IDS', [43007]);
define('SURVEYJS_USE_AGE_4_ORG_IDS', [43007]); //SurveyJS表单中使用age而不是birthday
define('NOVONORDISK_CUSTOMFIELD_FPG', 10288);
define('NOVONORDISK_CUSTOMFIELD_RPG', 10289);
define('NOVONORDISK_CUSTOMFIELD_HBALC', 10290);
define('NOVONORDISK_CUSTOMFIELD_APG', 10291);

// SME-131 恒瑞
define('HENGRUI_ORG_IDS', [43050]);

//体之健客户ids
define('TZJ_COSTOMER_IDS', [23, 24, 25]);
define('IKANG_ORG_IDS_LEGAL', []);
//护心宝
define('DCG_PCODE_PREFIX', '6000');

// Hospital
define('HOSPITAL_CUSTOMER_IDS', [8, 11, 14]);

//
define('FD16_LINE_UP_VERSION', 'v1.02.04.61_cn');

//复兴康养
define('FUXINGKANGYANG_ORG_ID', 40394);

//FD16-admin
define('FD16_ADMIN_URl', 'https://test-fd16-admin.airdoc.com');
//voice
define('ICVD_VOICE', 'Xn_KfJw8FHiWUC3KevbTA892Y59II2nuIxbmHRztT14');
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
//账号迁移白名单
define('TRANS_USER_WHITE_LIST', [
    1, //海龙
    5022, //秦勇
    18673, //姚光远
    22683 //张岚
]);

//常用部门ID
define('DEPARTMENT_PU', 1066); //一部PU 1066：陈飞 13910416240  已弃用
define('DEPARTMENT_MU', 1068); //二部MU 1068 ：秦勇 18811060560 已弃用
define('DEPARTMENT_RU', 1065); //三部RU 1065：姚光远 1381777792  已弃用
define('DEPARTMENT_SME', 1142); //SME 1142 ：秦勇 18811060560
define('DEPARTMENT_KA', 1141); //KA 1141 ：姚光远 1381777792
define('DEPARTMENT_MPC', 1144); //MPC（欢曈） 1144 ：陈飞 13910416240
define('DEPARTMENT_OBU', 1145); //OBU（海外） 1145 ：张岚 15116991725

//BAEQ-3186 指定机构直接展示手机号
define('PHONE_ORG_IDS', [41738]);

//微信第三方开放平台
define('COMPONENT_APPID', 'wx4468a8669d2c2ef7');
define('COMPONENT_APPSECRET', 'cf93644e2b00e6672d255291c6cf9b5d');
define('COMPONENT_ENCODING_AESKEY', 'a3f9fdsutn5DjfdsieD04FJsdknvbfdF3bAmkpqs2cW');
define('COMPONENT_TOKEN', 'AIRDOCGDZ');

define('HUANTONG_API', 'https://test-api-huantong.airdoc.com/');

//鹰瞳收费宝接口地址
define('ADC_PAY_URL', 'https://openapi-adc.airdoc.com'); //线上环境

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
