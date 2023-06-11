<?php
require_once(__DIR__ . '/config.crypt.php');
define('ENV', 'production');
define('PREFIX_CACHE', '');
define('HUIXINTONG_FULL_NAME', '慧心瞳健康评估');
define('YINGTONG_FULL_NAME', '鹰瞳健康');
define('ZHONGYOU_FULL_NAME', '众佑健康评估');
define('SHORT_URL', 'http://s.airdoc.com');
define('EYE_DOMAIN', 'http://ikang.airdoc.com/');
define('BISHENG_DOMAIN', 'http://innerapi-bisheng.airdoc.com/');
define('FANGYUAN_DOMAIN', 'http://innerapi-fangyuan.airdoc.com');
define('EYE_DOMAIN_HTTPS', 'https://ikang.airdoc.com/');
define('EYE_DOMAIN_HTTPS_PE', 'https://pe.airdoc.com/');
define('EYE_DOMAIN_HTTPS_PE002', 'https://pe.jkhrs.com/');
define('EYE_DOMAIN_HTTPS_OVERSEA', 'https://global.airdoc.com/');
define('EYE_DOMAIN_LOCAL', 'https://pe.airdoc.com/');
define('IMG_DOMAIN', 'http://img.airdoc.com/');
define('IMG_DOMAIN_HTTPS', 'https://img3.airdoc.com/');
define('IMG_DOMAIN_NEW', 'http://img6.airdoc.com/');
define('IMG_DOMAIN_NEW_HTTPS', 'https://img6.airdoc.com/');
define('SAAS_HTTPS', 'https://saas.airdoc.com/');
define('IMG_SWITCH', 1);
define('YUANMENG_2023', [43182]);
define('LOGIN_LIMIT', 10);
define('MODEL_SERVICE', 'http://172.17.10.103/');
define('MODEL_SERVICE_STAGING', 'http://172.17.10.103/');
define('ALGO_DISPATCH', 'http://algo-dispatch.airdoc.com/');
define('MODEL_RETINA_EX', 'http://172.17.67.254/');
define('MODEL_RETINA_EX_STAGING', 'http://172.17.67.254/');
define('DP_NIGHT', '23:00:00');
define('DP_MORNING', '07:00:00');
//define('FEEDBACK_DOMAIN', 'http://39.96.221.105/');
define('FEEDBACK_DOMAIN', 'http://feedback.airdoc/');
define('EMAIL_HOST', 'smtp.office365.com');
define('EMAIL_USER_NAME', 'report@airdoc.com,report02@airdoc.com,report03@airdoc.com,report04@airdoc.com,report05@airdoc.com');
define('PA_CRYPT_KEY', '12345678');

// SME业务系统
define('SHEN_NONG_DOMAIN', 'https://aisp.airdoc.com/api');
define('SHEN_NONG_OVERSEA_DOMAIN', 'https://aisp-global.airdoc.com/api');
define('SHEN_NONG_USERNAME', 'airdoc-robot');
define('SHEN_NONG_PASSWORD', '4Pd1e6W8z6bvtyPZc1V4tgh8MxC187Zn');
define('SHEN_NONG_OVERSEA_USERNAME', 'airdoc-robot');
define('SHEN_NONG_OVERSEA_PASSWORD', '9w#tlHtSFqczF%41WQ9mK5^hqIOTMSdW');

define('MAX_CRYPT_CHECK_ID', 4598200);

define('OVERSEA_ENCRYPT_KEY', '448b0fbe1a894050e9447ed79a87d9a4');

//Airdoc人工智能
define('WX_APPID', 'wx5c17f5b7a6db9bd6');
define('WX_TOKEN', '1b0381d13b01674b97fb5a2e47849b2b');
define('WX_OPENID_PREFIX', 'oI5hi');
define('WX_SECRET', '1891604693716ee2e3254155a88fa672');
define('WX_REPORT_TEMPLATE_ID', 'qcodqN4dJ9nM7P3fYTvZ6p5h5Y_iBuuLbq8Qs2fdCPw');
define('WX_BUY_SUCCESS_TEMPLATE_ID', 'mJPeAzTn5d0n6R9nwU70JHRhSKwbm7tlWvgHJY-s7Vc');
define('WX_SUBSCRIBE_ID', 'DAuQj0zeWqpnqF7DZAiNwTFNh4uPqKj0T_RngbBmXeY');
define('WX_SHOT_FAIL_ID', 'fbl8vXTTe1jBWIT8hUYrPOu1GEP2Ov5RJAwsTq-1-cw');
define('WX_HEALTH_WARNING', 'KXjSf0cqZjS7-X8AxjeGO5qLA7LC3fv89zlLUKGhu_8');
define('WX_ACTIVITY_ID', 'X5yvHY1piFuAbCsQfwZw-2lkOuWNjILR2RERe5JVflM');
define('WX_REGISTER_TEMPLATE_ID', '6Rek3Guti_lzO8D_U0zn8usk-OCObKh8-Y23aioDxb4');
define('WX_NIGHT_DELAY_NOTICE_TEMPLATE_ID', 'TyGwiEKCPVmx1frXQKe7QAXB1TK7kGynSQ3zWCFtebE');
define('WX_RESCAN_TEMPLATE', '-kZCAyzQnj3-rgSwGZLwfogElkDmtYhIbURzOgOLJkw');

define('WX_SWITCH_TIME', '2018-12-10 19:00:00');

//北京爱康国宾
// define('WX_APPID_NEW', 'wxf4c796f226489c3d');
// define('WX_OPENID_PREFIX_NEW', 'o657O');
// define('WX_SECRET_NEW', 'd4258c5e7c77e8ee3c364d35f677d6dc');
// define('WX_REPORT_TEMPLATE_ID_NEW', 'YD0YPhtBlDU-grIu9nFvETlDkNbtO0VeENuqpySWBRo');
// define('WX_BUY_SUCCESS_TEMPLATE_ID_NEW', 'D4fsuDCx9JLV3EWQhBKuO2fbXoh58uQMbGEpv1lZ3YI');
// define('WX_SUBSCRIBE_ID_NEW', 'voKctpGwBAhPFotZfacDvnx8C_xGoRaKGVtntFQKkVY');
// define('WX_HEALTH_WARNING_NEW', 'ESgOz9mxhLfjFgZos8nJ6P8lA_5cswc-BlYvQf-qfHg');
// define('WX_ACTIVITY_ID_NEW', '9RjBnbUjl9SHZ-hebJFUAH9byYATSvYnEKrhfvlXG_k');
// define('WX_REGISTER_TEMPLATE_ID_NEW', 'SDSISM1A748S0Di9RnhihpNs_y3A2_E4Abltpdk0zeE');
// define('WX_NIGHT_DELAY_NOTICE_TEMPLATE_ID_NEW', '');

// 爱康信公众号，管理员是Emma，长期运营是李行小号微信
// 注册使用邮箱：ak_airdoc@163.com 、 iKang_airdoc
define('WX_APPID_NEW', 'wx869bca366efcc4be');
define('WX_OPENID_PREFIX_NEW', 'oTS0h');
define('WX_SECRET_NEW', '4bd881c6dd8235a4f72cbeab93751498');
define('WX_REPORT_TEMPLATE_ID_NEW', 'FhzSIV4xJYuFP9fdmtnUscA41CBk3Tf0QWSl17n-sxg');
define('WX_BUY_SUCCESS_TEMPLATE_ID_NEW', 'Ij3vNO7X9YDaTrMv17OAp_sl5FMChOzJsjldkQXk8k0');
define('WX_SUBSCRIBE_ID_NEW', 'tgLCc5kK1jCKtKEQ6a4ye0lZpLL6ioNwI5KugPlZWaE');
define('WX_HEALTH_WARNING_NEW', 'h9hCOiYtbE5SCNTpFiFIoDu_7Wx0C476JY9LYkPJ97w');
define('WX_ACTIVITY_ID_NEW', 'w1moxm7mZDLIlnCfStAZvaqFtko2_JBObN65Wldz5yA');
define('WX_REGISTER_TEMPLATE_ID_NEW', 'TCaYb52IzrvhWoKwfcOnp2ytPdmvijXny5dN25GtQ30');
define('WX_NIGHT_DELAY_NOTICE_TEMPLATE_ID_NEW', 'SiOa2ekhTX-VMuUCs7qwPj5-UvV-ky_GAWJv2q0Igr0');
define('WX_SHOT_FAIL_ID_NEW', 'zLwLaA_YbnBiIsstvFEwzHiiwGGs0khoN3kyGla_XE0');

define('RI_ORG_IDS', [43119]);

define('PRINT_APP_ID', 'fcb59834d8d1');
define('UPDATE_DIAGNOSE_WHITE', [1, 5010, 5011, 5184]);
define('PRINT_APP_SECRET', '410eafd32de347afb8e2');
define('PRINT_EQUIPMENT_ID', '4748a34961a664a9');
define('PRINT_BASEURL', 'https://print.airdoc.com');
//平安医保科技
define('PA_APP_ID', 'PT100000004747');
define('PA_APP_SECRET', 'r382934s23e38!2a');
define('PA_DOMAIN', 'https://city.pingan.com.cn/');

define('UPGRADE_B_FEE', '6900');
define('UPGRADE_C_FEE', '9900');

define('OSS_BUCKET', 'adc-fundus');
define('OSS_ENDPOINT', 'oss-cn-beijing.aliyuncs.com');
define('OSS_ENDPOINT_SHANGHAI', 'oss-cn-shanghai.aliyuncs.com');
define('IMG_SWITCH_OSS', 1);
define('IMG_DOMAIN_OSS', 'https://img8.airdoc.com/');
define('IMG_DOMAIN_OSS_VPC', 'http://adc-fundus.oss-cn-beijing-internal.aliyuncs.com/');
define('IMG10_DOMAIN_OSS', 'https://img10.airdoc.com/');
define('IMG10_DOMAIN_OSS_VPC', 'http://airdoc-image.oss-cn-beijing-internal.aliyuncs.com/');
define('IMG_DOMAIN_OSS_SAT', 'https://airdoc-sat.oss-cn-shanghai.aliyuncs.com/');
define('IMG_DOMAIN_OSS_SAT_VPC', 'https://oss-cn-shanghai-internal.aliyuncs.com/');

define('AK_API_DOMAIN', 'https://reportapi.health.ikang.com/');
define('AK_H5_DOMAIN', 'https://report.health.ikang.com/');
define('TJB_H5_DOMAIN', 'https://report.tijianbao.com/');

define('PUHUIBAO_ORG_IDS', [42960, 42961]); // 普惠保

// SAFE_HOUSE_DOMAIN
define('SAFE_HOUSE_DOMAIN', 'https://safe-house.airdoc.com/');

// SMB
define('CAMERA_IOT_DOMAIN', 'http://fd16-iot.airdoc.com/');
define('FD16_ORG_ID', 40064);
define('FD16_ORG_ID_ARY', [40077]);

// 众佑 - 使用鹰瞳健康公众号 ICVD
// define('ZY_WX_APPID', 'wxbe2ca857a8729b4f');
// define('ZY_WX_TOKEN', '47e5fc368c334feb9176a0e56cddae79');
// define('ZY_WX_OPENID_PREFIX', 'oBSa9');
// define('ZY_WX_SECRET', '836ea95746209af523e1b309c2cfe6b4');
// define('ZY_WX_SUBSCRIBE_ID', '1IncPlsU8x5wOricsWtKyGcGw1_MYcg2BwQhuanQ_kk');
// define('ZY_WX_SHOT_FAIL_ID', 'QS8TnKqZ3TbL_uF_E4tW7J0xPY9_BI9zODdupNyQnMs');
// define('ZY_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', '30sP3ZdmcnVM3azUjGEPt1AGfyoIT3IBVBPkDErubxo');
// define('ZY_REPORT_NOTICE_TEMPLATE_ID', 'SeNFO4mCcAj3_O-ryR8Gp14xOETbS4vRePXGDkV3sak');
// define('ZY_REGISTER_TEMPLATE_ID', '-Hi6donTXAYatrAkw2FoIB9ef0FJxh1_FGk6V5mX0ZY');
// define('ZY_PCODE_PREFIX', '8996');
// ICVD
define('ICVD_WX_APPID', 'wxbe2ca857a8729b4f');
define('ICVD_WX_TOKEN', '47e5fc368c334feb9176a0e56cddae79');
define('ICVD_WX_OPENID_PREFIX', 'oBSa9');
define('ICVD_WX_SECRET', '836ea95746209af523e1b309c2cfe6b4');
define('ICVD_WX_SUBSCRIBE_ID', '1IncPlsU8x5wOricsWtKyGcGw1_MYcg2BwQhuanQ_kk');
define('ICVD_WX_SHOT_FAIL_ID', 'QS8TnKqZ3TbL_uF_E4tW7J0xPY9_BI9zODdupNyQnMs');
define('ICVD_REPORT_NOTICE_TEMPLATE_ID', 'SeNFO4mCcAj3_O-ryR8Gp14xOETbS4vRePXGDkV3sak');
define('ICVD_HEALTH_ADVICE_TEMPLATE_ID', 'cm5zlOYYKjr9-fYEshc2vYIs2FeZIyhfSeMLda-aa_o');
define('ICVD_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', '30sP3ZdmcnVM3azUjGEPt1AGfyoIT3IBVBPkDErubxo');
define('ICVD_ORG_ID', 40071);
define('ICVD_ORG_ID_2', 40485);
define('PA_HFL_ORG_ID', [41637, 42226]);
define('ICVD_PCODE_PREFIX', '8997');
define('ICVD_ANLYZE', 0);
define('ICVD_REGISTER_TEMPLATE_ID', '-Hi6donTXAYatrAkw2FoIB9ef0FJxh1_FGk6V5mX0ZY');
define('ICVD_REVIEW_NOTICE_TEMPLATE_ID', '_zmR2l0yyPKrmopVEInQshmwII97_v6yR8MzWM4BYr8');

//voice
define('ICVD_VOICE', 'w9W1oo8sCqJFNf3Q2o2hlF4x40qctloegaZCnjkhwlY');
define('SKB_ORG_ID', 40625);
define('SKB_ORG_ID_YT', 40626);

// 众佑 - 丁星辰 gongzhonghao@airdoc.com A123
define('ZY_WX_APPID', 'wxc93456c256a75f34');
define('ZY_WX_TOKEN', 'd7174cbb441a0439d0693c729b033bec');
define('ZY_WX_OPENID_PREFIX', 'oYOrv');
define('ZY_WX_SECRET', 'c27ee1074b13ddc97077e250211f2e17');
define('ZY_WX_SUBSCRIBE_ID', 'nrxt_A0v5vZVoaWra0NM4yxN9zAve2y4vUsdDplTzHI');
define('ZY_WX_SHOT_FAIL_ID', 'W6yCd2xYM30Fa4aZDApdTUpS3vHlKndKzRL79O-4hiI');
define('ZY_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', 'yCNJMe-7ckSWU39ugar9xY7KQy21dXnNy2BsaSEN1UM');
define('ZY_REPORT_NOTICE_TEMPLATE_ID', 'zbsFuknDvoE0SALPBA9VS7tqT2rwSFxk2iguhykZaJc');
define('ZY_REGISTER_TEMPLATE_ID', 'c5wjO6x3FCZr5lMMDk6f2aSTCKNzUw3eLp9g9fXPI9w');
define('ZY_PCODE_PREFIX', '8996');

define('ZX_PCODE_PREFIX', '8981');

define('ZY_CUSTOMER_IDS', [15, 17]);

define('CHUNYU_ORG_ID', 40075);
define('INTERNAL_SECRET', 'AirdocCTWFUS001');
define('TAIPING_ORG_ID', 40087);
define('PHARMACY_ORG_ID', 40088);
define('IKANG_ORG_IDS_LEGAL', [5127, 5111]);

// RISK
define('RISK_TIME', '2020-05-14 22:00:00');
define('YT_RISK_TIME', '2020-05-23 00:00:00');
define('SUMMARY_RISK_TIME', '2020-07-02 21:00:00');
define('FD16_BAOWEN_TIME', '2020-08-01 19:00:00');
define('BAOWEN_V2_TIME', '2020-08-21 22:00:00');
define('BAOWEN_V3_TIME', '2020-09-04 22:20:00');
define('DBHT_RISK_TIME', '2020-08-11 00:00:00');
define('USABLE_DISEASES_TIME', '2020-08-24 22:00:00');
define('DOUBLE_BAD_V2_TIME', '2020-09-04 22:20:00');
define('DID_CONSISTENCY_TIME', '2021-03-20 19:00:00');
define('MPOD_DID_TIME', '2022-09-26 20:30:00');


define('HISTORY_INTERVAL', 1);
// 算法接管风险
define('RISK_V2_TIME', '2020-11-07 00:00:00');
define('COMPREHENSIVE_RISK_ORG_ID', [1, 40264, 40159]);

define('RISK_SMOKE_TIME', '2020-12-01 20:45:00');

// AGENT
define('AGENT_TOOL_DOMAIN', 'https://agent-tool.airdoc.com/');
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
define('ENCRYPT_KEY_ZY_PA_ADS', 'mi2nRcECNyrGVIMQ');

define('ANEMIA_ORG_ID', 40128);
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
define('NEW_TEMPLATE_15', 'pc-v2/print-ytmedical/'); // 鹰瞳医疗
define('NEW_TEMPLATE_16', 'pc-v2/ytHeathyPdf?en_check_id='); //鹰瞳健康
define('NEW_TEMPLATE_17', 'pc-v2/print-ytmedical/'); // 鹰瞳医疗经典版
define('NEW_TEMPLATE_18', 'pc-v2/print-ytmedical/'); // 海外鹰瞳医疗
define('NEW_TEMPLATE_19', 'pc-v2/ythPdf/index?en_check_id='); //鹰瞳健康2.0
define('NEW_TEMPLATE_20', 'pc-v2/print-ytmedical/'); // 鹰瞳KA/SME

define('NEW_TEMPLATE_MEDICAL_HEATHY', 'pc-v2/ytHeathyPdfPE?en_check_id='); // 鹰瞳医疗健康风险
define('NEW_TEMPLATE_OVERSEA_MEDICAL_HEATHY', 'pc-v2/ytHeathyPdfPE?en_check_id='); // 海外鹰瞳医疗健康风险

// 好数科技
define('HAOSHU_APP_ID', '12005');
define('HAOSHU_APP_KEY', '3b906fe26ff4eec');
define('HAOSHU_APP_SECRET', '8a25e039fd06d35729f0e3c4bb21bd87');
define('HAOSHU_CALLBACK_URL', 'https://pe.airdoc.com/api/fd16/receiveAuthCode');

//CMS
define('CMS_DOMAIN', 'https://cms-admin.airdoc.com/');
define('ADS_SWITCH', 1);
define('ROMA_DOMAIN', 'https://innerapi-roma.airdoc.com');

//禾连
define('HELIAN_ORG_ID', 40161);
//九州速药
define('JZSY_ORG_ID', 40188);
// 宝石花
define('BSH_ORG_ID', 40174);
// 西藏
define('TIBET_ORG_ID', 40185);

define('PK_ORG_IDS', [40395]);
define('AGENT_VIP_ORG_IDS', [40692, 40661]);

// 中信保诚
define('CITIC_ORG_ID', 40183);
define('CITIC_API_HOST', 'https://sqs.citicpruagents.com.cn/aihome/');
define('CITIC_APPID', '10000000');
define('CITIC_SECRET', '735b86aca3a92717d07f5775a0378c29');
define('CITIC_KEY', 'ac1e8a04dbef43d2bd2c3d2ad04969d9');
define('CITIC_QUERY_REPORT_URL', 'http://tijian.52190.com/query.html');

// 运营工具
define('CV_DOMAIN', 'https://cv-admin.airdoc.com/');

//STI
define('STI_ORG_ID', 40139);
// define('STI_SYNC_URL', 'https://brain.youlanai.cn/v1/device/airdoc/setReport');
define('STI_SYNC_URL', 'https://api.sti-medical.cn/common-api/airdoc/setReport');

// 筛查登记小程序
define('REGISTER_WX_APPID', 'wxe15965084f03c697');
define('REGISTER_WX_SECRET', 'ddc73424a0743638505e472643b43b29');
// SAT小程序
define('SAT_WX_APPID', 'wx9edb755c16cad8eb');
define('SAT_WX_SECRET', '3210be713b5ee3a63ed475479cf89107');
// AK-1728
define('SINGLE_BAD_TIME', '2020-10-30 21:30:00');

// 筛查登记小程序开关
define('SWITCH_REGISTER_MINIPROGRAM', 0);

// FD16设备控制小程序
define('FD16_WX_APPID', 'wxd8cdabdc2c7f432a');

// Army
define('ARMY_ORG_ID', ['40313']);

define('VCODE_ORG_ID', [40338, 40337, 40410]);
define('TAIBAO_ORG_ID', [40338]);
define('TAIKANG_ORG_ID', [40337, 40410]);
define('TAIKANG_ZY_ORG_ID', [40410]);
define('TAIKANG_YTH_ORG_ID', [41954]);
define('TAIKANG_HXT_ORG_ID', [40794]);
define('HK_KYB_ORG_IDS', ['42962']); //香港快验保

// 合谐医疗 SME-35 已支持在盘古机构配置中修改价格，其余机构价格在此配置
define('SALESMAN_PRICE_DEFAULT', 398); //业务员登记页面默认的支付价格
define('SALESMAN_ORIGIN_PRICE_DEFAULT', 998); //业务员登记页面默认的原价

// SME-80 江西新华 风险分组
define('JIANGXI_XINHUA_ORG_IDS', [43165]);

// 诺和诺德自定义问卷 SME-67
define('NOVONORDISK_ORG_IDS', [43112]);
define('SURVEYJS_USE_AGE_4_ORG_IDS', [43112]); //SurveyJS表单中使用age而不是birthday
// 诺和诺德自定义问卷 SME-67
define('NOVONORDISK_CUSTOMFIELD_FPG', 325);
define('NOVONORDISK_CUSTOMFIELD_RPG', 326);
define('NOVONORDISK_CUSTOMFIELD_HBALC', 327);
define('NOVONORDISK_CUSTOMFIELD_APG', 328);

// SME-131 恒瑞
define('HENGRUI_ORG_IDS', [43277]);

// Hospital
define('HOSPITAL_CUSTOMER_IDS', [8, 11, 14]);

// 护心宝 - 李兴龙
define('DCG_WX_APPID', 'wxe3814b2175aa81bd');
define('DCG_WX_TOKEN', '7bc51b94e33e6f7171d9937dd2d9a49e');
define('DCG_WX_OPENID_PREFIX', '');
define('DCG_WX_SECRET', 'bc709bfab5fab12d17d60bfd6f833846');
define('DCG_WX_SUBSCRIBE_ID', '');
define('DCG_REPORT_NOTICE_TEMPLATE_ID', '');
define('DCG_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', '');
define('DCG_PCODE_PREFIX', '6000');
//体之健客户ids
define('TZJ_COSTOMER_IDS', [23, 24, 25]);
//护心宝 -小程序
define('DCG_APPLETS_APPID', 'wx8ff7153e89e7a394');
define('DCG_APPLETS_SECRET', 'f7f33b3407b3dcca3f26a772ca16dbaa');

// 支持排队的FD16固件版本
define('FD16_LINE_UP_VERSION', 'v1.02.04.61_cn');
//FD16-admin
define('FD16_ADMIN_URl', 'https://fd16-admin.airdoc.com');
//导流机构
define('DISTRIBUTION_ORG_ID', 40597);
//华泰
define('HUATAI_ORD_ID', 40722);

define('TZJ_WX_APPID', 'wx36c59e8da01a22ae');
define('TZJ_WX_TOKEN', '1b0381d13b01674b97fb5a2e47849b2b');
define('TZJ_WX_OPENID_PREFIX', 'oVfPy');
define('TZJ_WX_SECRET', 'da68ed121b13b6a2e8afb88327e94c9e');
define('TZJ_WX_REGISTER_TEMPLATE_ID', 'pIDUIFDBAT5aX88IJLAorcLgMlP1d3y16jXFSSUq78A');
define('TZJ_FULL_NAME', '体知健健康评估');
define('TZJ_REPORT_NOTICE_TEMPLATE_ID', 'AdADp-hsdSaGR8OdkpbXgV6CEDXiQ647ruAoXkuOC20');
define('TZJ_WX_SUBSCRIBE_ID', 'VXD-CMIpGafdz9-6cal-1Karw-V_RSBfEXYqoN18fUM');
define('TZJ_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID', 'yQJIEPeM_QdSKn4WB319Klc5623V69HZUH3uOUgDXgY');
define('TZJ_WX_HEALTH_WARNING', '2H84IsUP7V4_bhh-HSnZcJLQBG6GYV-VZBfY-lS1l4Y');
define('TZJ_WX_SHOT_FAIL_ID', '4HmNlpt62LqLauhOzm_owCzjMxMfqZLXuKsI5Nun2bU');
define('TZJ_WX_HEALTH_MANAGEMENT_REMIND_ID', 'YhC6ZXFR0zM8nPuKp9Oje2UgZ3Qlz-YZ76hPgkKbJSU');

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

define('YANGGUANG_APPKEY', '90e60cacbffd477e9512a0839aacb282');
// BAEQ-3186 YTMED-50 指定机构直接展示手机号
define('PHONE_ORG_IDS', [
    41738, 41780, 41734, 41889, 41761, 41935, 41911, 41900, 41883,
    41749, 41628, 41626, 41688 // YTMED - 135 湘雅相关机构
]);

//微信第三方开放平台
define('COMPONENT_APPID', 'wx49a4a3e850493fbb');
define('COMPONENT_APPSECRET', '1822318c62651aa4210a17688dd393e2');
define('COMPONENT_ENCODING_AESKEY', 'a3f9fdsutn5DjfdsieD04FJsdknvbfdF3bAmkpqs2cW');
define('COMPONENT_TOKEN', 'AIRDOCGDZ');

//账号迁移白名单
define('TRANS_USER_WHITE_LIST', [
    1, //海龙
    5022, //秦勇
    18673, //姚光远
    22683 //张岚
]);

define('HUANTONG_API', 'http://api.huantong.airdoc/');

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
