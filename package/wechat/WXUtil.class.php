<?php

namespace Air\Package\Wechat;

use \Air\Package\Wechat\Helper\RedisCheckQRCode;
use \Air\Package\Wechat\Helper\RedisWechat;
use \Air\Libs\Base\Utilities as Util;
use Air\Package\Checklist\CheckLog;
use \Air\Package\Checklist\Helper\DBCheckInfoHelper;
use Air\Package\Checklist\Helper\RedisImageUrl;
use Air\Package\Checklist\Helper\RedisLock;
use Air\Package\Checklist\Image;
use \Air\Package\wechat\WechatTag;
use Air\Package\User\Organizer;
use Phplib\Tools\Logger;

class WXUtil
{
    private $appId = WX_APPID;
    public $wxa = null;
    private $secret = WX_SECRET;
    private $baseTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    private $authorizeUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=airdoc_login#wechat_redirect';
    private $openIdUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=airdoc_login#wechat_redirect';
    private $accessTokenUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
    private $ticketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
    private $authTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
    private $infoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
    private $callbackUrl = 'http://bot.cloud.gmw.cn/api/wxauth';
    private $redirectUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=register#wechat_redirect';
    private $userinfoUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
    private $refreshTokenUrl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s';
    private $sendTempMsgUrl = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=%s';
    private $getUserTags = 'https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=%s';
    private $getTempIdUrl = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=%s';
    private $createMenuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s';
    private $createConditionalMenuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/addconditional?access_token=%s';
    private $deleteConditionalMenuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/delconditional?access_token=%s';
    private $getConditionalMenuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/trymatch?access_token=%s';
    private $deleteMenuUrl = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=%s';
    private $createQRCodeUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=%s';
    private $createTagsUrl = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token=%s';
    private $getTagsUrl = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token=%s';
    private $makeTagsUrl = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=%s';
    private $change_openid_url = 'https://api.weixin.qq.com/cgi-bin/changeopenid?access_token=%s';

    // --------------------,
    private $wechatTags = ['pushed', 'received'];
    private $wechatTagsOld = [
        '男', '女', '1-39岁', '40-50岁', '50-65岁', '65岁以上', '企业', '社区老人', '医院', '省份', '糖尿病性视网膜病', '动脉硬化/高血压视网膜', '青光眼',
        '白内障疑似/图片模糊', '年龄相关性黄斑变性', '近视性视网膜', '其他黄斑部异常', '视网膜血管阻塞', '玻璃体疾病', '脉络膜疾病', '视神经疾病', '其他疾病', '眼睛干涩', '高血压', '高血脂',
        '糖尿病', '河北', '天津', '北京'
    ];
    private $uploadMediaUrl = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=%s";
    private $uploadpermanentImageMediaUrl = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=%s&type=%s";
    const QR_PARAMS = [
        'is_new', //0：慧心瞳；1：爱康微信
        'req_id', //代理人工号
        'pcode', //体检号
        'ins_v2', //保险v2
        'noid', //不需要身份证号码
        'bv', //安贞、北医心血管数据
        'sn', //B套餐fd16的sn号的MD5,
        'is_fd16', // fd16参数
        'org_id', // 机构ID
        'age_type', // 年龄输入方式
        'show_fd16_video', // 视频展示
        'show_fd16_qrcode',
        'work_mode',
        'tibet',
        'is_zhongyou',
        'is_yingtong',
        'pay_price',
        'register_type', // 0后填；1先填；
        'is_ak_outside', // 爱康外检
        'substr6Sn', //添加参数赛选
    ];
    const REPORT_TYPE_NAME = [
        'yingtong' => '鹰瞳健康',
        'huixintong' => '慧心瞳',
        'zhongyou' => '众佑',
    ];
    //使用第三方公众号启动相机
    const THIRDS_ORG_IDS = [
        'yingtong' => [ //
            '41410',
            '41204',
            '42709', // 大庚科技-健康
            //'40337',
        ],
        'zhongyou' => [
            // '40967',
            // '40761',
            // '41080'
        ],
        'tizhijian' => [
            '41026',
            '41242',
            '41023'
        ],
        'huixintong' => [],
        'yt_health' => [
            '42709', // 大庚科技-健康
        ],

    ];

    const OP_PHONES = ['13269339162', '13031005474'];
    public function  __construct($appid = '', $secret = '')
    {
        if ($appid) {
            $this->appId = $appid;
        }
        if ($secret) {
            $this->secret = $secret;
        }
        $this->wxa = new WXAUtils($this);
    }
    public function  __set($name, $val)
    {
        $this->$name = $val;
    }
    public function  __get($name)
    {
        return $this->$name;
    }

    public function getRedirectUrl($url = '', $is_new_wechat = 1)
    {
        if (!$url) {
            if ($is_new_wechat == 1) {
                $url = EYE_DOMAIN . 'api/wechat/checklist?is_new=1';
            } elseif ($is_new_wechat == 3) {
                $url = EYE_DOMAIN_HTTPS_PE . 'api/wechat/checklist?is_new=3';
            } elseif ($is_new_wechat == 4) {
                $url = EYE_DOMAIN_HTTPS_PE . 'api/wechat/checklist?is_tizhijian=1';
            } elseif ($is_new_wechat == 7) {
                $url = EYE_DOMAIN_HTTPS_PE . 'api/wechat/checklist?is_yt_health=1';
            } else {
                $url = EYE_DOMAIN . 'api/wechat/checklist?is_new=0';
            }
        }
        return sprintf($this->redirectUrl, $this->appId, urlencode($url));
    }
    static public function curl($url, $p = array(), $isPost = 0, $is_json = 0, $protocol = 'http', $is_buffer = 0)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            if ($is_json) {
                curl_setopt($ch, CURLOPT_POSTFIELDS,  json_encode($p, JSON_UNESCAPED_UNICODE));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($p));
            }
        }
        if (strtolower(substr($url, 0, 8)) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        $result = curl_exec($ch);
        if (empty($result)) {
            $result = curl_exec($ch);
        }
        $ret = json_decode($result, 1);
        if (!$is_buffer) {
            Logger::info([$url, $result], 'wechat_curl');
            return $ret;
        } elseif ($result && !$ret) {
            return $result;
        } else {
            Logger::info([$url, $result], 'wechat_curl');
            return false;
        }
    }

    public function getCode()
    {
        $url = sprintf($this->authorizeUrl, $this->appId, urlencode(EYE_DOMAIN . 'api/wechat/login'));
        return $url;
    }


    public function getOpenid($url, $code)
    {
        $openid = $ret_url = '';
        //通过code获得openid
        if (empty($code)) {
            //触发微信返回code码
            $ret_url = sprintf($this->openIdUrl, $this->appId, urlencode($url));
            return [$ret_url, $openid];
            // Header("Location: $url");
            // exit();
        } else {
            //获取code码，以获取openid\
            $data = $this->getAuthAccessToken($code);
            $openid = $data['openid'];
            return [$ret_url, $openid];
        }
    }

    private static $name_map = [
        ICVD_WX_APPID => '鹰瞳健康',
        ZY_WX_APPID => '众佑',
        WX_APPID => 'Airdoc人工智能',
        WX_APPID_NEW => '北京爱康国宾',
    ];

    public function getBaseAccessToken()
    {
        $key = 'access_token';
        $ret = $this->getSetting($key);
        $token = '';
        if (empty($ret)) {
            $url = sprintf($this->baseTokenUrl, $this->appId, $this->secret);
            $result = self::curl($url);
            if (!empty($result[$key])) {
                $check = $this->addSetting(array('key' => $key, 'value' => $result[$key], 'expires_time' => $result['expires_in']));
                $token = $result[$key];
            } else {
                $name = self::$name_map[$this->appId] ? self::$name_map[$this->appId] : $this->appId;
                Util::DDMonitor("P2-getBaseAccessToken failed 公众号：{$name}; " . json_encode($result), 'cloudm', TRUE);
            }
        } else {
            $token = $ret;
        }
        return $token;
    }

    public function getAuthAccessToken($code)
    {
        $url = sprintf($this->authTokenUrl, $this->appId, $this->secret, $code);
        $result = self::curl($url);
        return $result;
    }

    public function getAccessToken()
    {
        $key = 'auth_access_token';
        $refresh_token_key = 'refresh_token';
        $ret = $this->getSetting($key);
        $refresh_token = $this->getSetting($refresh_token_key);
        $token = '';
        if (empty($ret)) {
            $url = sprintf($this->refreshTokenUrl, $this->appId, $refresh_token);
            $result = self::curl($url);
            if (!empty($result[$key])) {
                $check = $this->addSetting(array('key' => $key, 'value' => $result[$key], 'expires_time' => $result['expires_in']));
                $token = $result[$key];
            }
        } else {
            $token = $ret;
        }
        return $token;
    }

    public function getTicket()
    {
        $key = 'jsapi_ticket';
        if ($this->is_ant) {
            $key = 'ant_' . $key;
        }
        $ret = $this->getSetting($key);
        if (empty($ret)) {
            $accessToken = $this->getBaseAccessToken();
            $url = sprintf($this->ticketUrl, $accessToken);
            $result = self::curl($url);
            if (!empty($result['ticket'])) {
                $this->addSetting(array('key' => $key, 'value' => $result['ticket']));
                $token = $result['ticket'];
            }
        } else {
            $token = $ret;
        }
        return $token;
    }

    public function addSetting($data)
    {
        if (isset($data['expires_time']) && !empty($data['expires_time'])) {
            if (is_array($data['value'])) {
                $data['value'] = json_encode($data['value']);
            }
            RedisWechat::setCache($this->appId . $data['key'], $data['value'], $data['expires_time'] - 600);
        } else {
            RedisWechat::setCache($this->appId . $data['key'], $data['value']);
        }
    }

    public function getSetting($key)
    {
        $val = RedisWechat::getCache($this->appId . $key);
        if (!$val) {
            $val = RedisWechat::getCache($this->appId . $key);
        }
        $decode = json_decode($val, 1);
        if (is_array($decode)) {
            return $decode;
        }
        return $val;
    }
    public function delSetting($key)
    {
        return RedisWechat::delCache($this->appId . $key);
    }

    public function getUserInfo($access_token, $openid)
    {
        $url = sprintf($this->userinfoUrl, $access_token, $openid);
        $result = self::curl($url);
        return $result;
    }

    public function getTempId($template_id_short)
    {
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->getTempIdUrl, $access_token);
        $data['access_token'] = $access_token;
        $data['template_id_short'] = $template_id_short;
        $result = self::curl($url, $data, 1, 1);
        if ($result['errcode'] == 0) {
            return $result['template_id'];
        }
        return '';
    }

    public function pushMessage($data, $checkinfo = [], $rec = 0)
    {
        if (!$data['access_token']) { //微信开放第三方wxkf消息推送兼容
            $access_token = $this->getBaseAccessToken();
            $data['access_token'] = $access_token;
        }
        $extra = [];
        if ($checkinfo['check_id']) {
            $extra = ['check_id' => $checkinfo['check_id']];
        }
        $url = sprintf($this->sendTempMsgUrl, $data['access_token']);
        $key = md5(json_encode($data));
        // 防止重复推送 BAEQ-3853
        $lock = RedisLock::lock($key, 30);
        if (!$lock) {
            Logger::error([$data, 'duplicate_send_avoid'], 'wechat_send_msg_error', $extra);
            return true;
        }
        $result = self::curl($url, $data, 1, 1);

        \Phplib\Tools\Logger::error([$data, $result], 'wechat_send_msg_error', $extra);
        if (in_array($result['errcode'], [40001, -1])) {
            $ret_del = $this->delSetting('access_token');
            if ($rec == 0) {
                Logger::error(['wechat repush log', $data, $result, 'check_id' => $checkinfo['check_id']], 'wechat_send_msg_error');
                $this->pushMessage($data, $checkinfo, 1);
            }
        }
        $check_log_remark = $data;
        unset($check_log_remark['access_token']);
        $check_log_remark = ['data' => ['param' => $check_log_remark, 'ret' => $result]];
        if ($result['errcode'] == 40001 || $result['errcode'] == 40003) {
            if ($checkinfo['check_id']) {
                CheckLog::addLogInfo($checkinfo['check_id'], 'wechat_send_msg_failed', $check_log_remark);
            }
            return 0;
        }
        if ($result['errcode'] == 0) {
            if ($data['template_id'] == WX_REPORT_TEMPLATE_ID || $data['template_id'] == WX_REPORT_TEMPLATE_ID_NEW) {
                $this->makeTagWrapper($data['touser']);
            }
            // if ($checkinfo['check_id']) {
            //     CheckLog::addLogInfo($checkinfo['check_id'], 'wechat_send_msg_sucess', $check_log_remark);
            // }
            return true;
        }
        if ($checkinfo['check_id']) {
            CheckLog::addLogInfo($checkinfo['check_id'], 'wechat_send_msg_failed', $check_log_remark);
        }
        return false;
    }

    public function makeTagWrapper($openid)
    {
        $tags = $this->getTags();
        $tagid = '';
        foreach ($tags['tags'] as $tag) {
            if ($tag['name'] == 'received') {
                $tagid = $tag['id'];
                break;
            }
        }
        $ret = 0;
        if ($tagid) {
            $ret = $this->makeTag4User($tagid, $openid);
        }
        return $ret;
    }
    public function deleteMenuNew()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 1);
        $url = sprintf($this->deleteMenuUrl, $access_token);
        return self::curl($url, [], 1, 1);
    }

    public function createMenuNew()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 1);
        $jd_url = "https://item.m.jd.com/product/36187697157.html?wxa_abtest=o&utm_source=iosapp&utm_medium=appshare&utm_campaign=t_335139774&utm_term=CopyURL&ad_od=share&ShareTm=AhqTw/uGjYlcr2DQdFAbJRnqSGyOZAqNmbyHoLnQeGWxyfdm%2BCR9K2JE%2BK6BN9Og%2BE3iwkOUCJMytiWEjENxMCOJOqasJq5pWGlFOgWX47SYCUBofpF03QioZ1ZsyfGuubdxvRko27S7URleSCMpyF7HunL8V4I/yIEwOFJ1aVo=&frm=iKangWechat";
        $data = [
            'button' => [
                // [
                //     'name' => '查看报告',
                //     'sub_button' => [
                //         [
                //             'type' => 'view',
                //             'name' => '我的报告',
                //             'url' => $url,
                //         ],
                //         [
                //             'type' => 'click',
                //             'name' => '报告咨询',
                //             'key' => "HELLO_AIRDOC",
                //         ],
                //     ],
                // ],
                // [
                //     'name' => '叶黄素',
                //     'type' => 'view',
                //     'url' => EYE_DOMAIN_HTTPS_PE . 'h5-v2/xanthophyll/index?source=h5&type=5',
                // ],
                [
                    'name' => '报告查询',
                    'type' => 'view',
                    'url' => EYE_DOMAIN_HTTPS_PE . 'thirdparty/query?channel=ikang',
                ],
            ]
        ];
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function deleteMenuDcg()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 1);
        $url = sprintf($this->deleteMenuUrl, $access_token);
        return self::curl($url, [], 1, 1);
    }
    public function getRedirectUrlDcg()
    {
        $url = EYE_DOMAIN_HTTPS_PE . 'api/dcg/wechat_checklist';
        return sprintf($this->redirectUrl, $this->appId, urlencode($url));
    }
    public function createMenuDcg()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrlDcg();
        $data = [
            'button' => [
                // [
                //     'name' => '查看报告',
                //     'sub_button' => [
                //         [
                //             'type' => 'view',
                //             'name' => '我的报告',
                //             'url' => $url,
                //         ],
                //         [
                //             'type' => 'click',
                //             'name' => '报告咨询',
                //             'key' => "HELLO_AIRDOC",
                //         ],
                //     ],
                // ],
                [
                    'name' => '检测状态',
                    'type' => 'click',
                    'key' => 'CHECK_STATUS',
                ],
                [
                    'name' => '心电检测',
                    'sub_button' => [
                        [
                            'type' => 'click',
                            'name' => '领取采集器',
                            'key' => "COLLECT",
                        ],
                        [
                            'type' => 'click',
                            'name' => '绑定采集器',
                            'key' => "BINDING",
                        ],
                    ],
                ],
                [
                    'name' => '查看报告',
                    'type' => 'view',
                    'url' => $url,
                ],
            ]
        ];
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }
    public function thirdDeleteMenuNew()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 1);
        $url = sprintf($this->deleteMenuUrl, $access_token);
        return self::curl($url, [], 1, 1);
    }
    public function thirdCreateMenuNew($data)
    {
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }
    public function tzjDeleteMenuNew()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 1);
        $url = sprintf($this->deleteMenuUrl, $access_token);
        return self::curl($url, [], 1, 1);
    }
    public function tzjCreateMenuNew()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 4);
        $data = [
            'button' => [
                [
                    'type' => 'view',
                    'name' => '我的报告',
                    'url' => $url,
                ],
                // [
                //     'name' => '叶黄素',
                //     'type' => 'view',
                //     'url' => EYE_DOMAIN_HTTPS_PE . 'h5-v2/xanthophyll/index?source=h5&type=4',
                // ],
                // [
                //     'name' => '爱眼日专场',
                //     'type' => 'view',
                //     'url' => 'https://shop42585899.m.youzan.com/wscshop/showcase/feature?alias=4QKXDJnbUk&kdt_id=42393731',
                // ],
                // [
                //     'type' => 'click',
                //     'name' => '报告解读',
                //     'key' => "HELLO_AIRDOC",
                // ],
            ]
        ];
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }
    public function ytHealthDeleteMenuNew()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 7);
        $url = sprintf($this->deleteMenuUrl, $access_token);
        return self::curl($url, [], 1, 1);
    }
    public function ytHealthCreateMenuNew()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 7);
        $data = [
            'button' => [
                [
                    'name' => '公司介绍',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '公司介绍',
                            'url' => 'https://mp.weixin.qq.com/s?__biz=Mzg2NDc3MTQyOA==&mid=2247483670&idx=1&sn=aca51dfde0354836ad646b702786061c&chksm=ce650d1df912840be75725426caa9b72edf1503068ce55db8d9cca6d63ecb545807de42202a3#rd',
                        ],
                        [
                            'type' => 'view',
                            'name' => '医学原理',
                            'url' => 'https://mp.weixin.qq.com/s?__biz=Mzg2NDc3MTQyOA==&mid=2247483672&idx=1&sn=36c9fdccdee34f12b0ee59d38fb7f6c3&chksm=ce650d13f9128405ee51618d7da80b47406f8498d3609a06ab676826e5e53bd01f9d46e7e604#rd',
                        ],
                        [
                            'type' => 'view',
                            'name' => '产品介绍',
                            'url' => 'https://mp.weixin.qq.com/s?__biz=Mzg2NDc3MTQyOA==&mid=2247483684&idx=1&sn=c75b6ef8e391e1af9bd612c700e3b030&chksm=ce650d2ff9128439c8404f14b151af3dc8a3450a654252da5dd1f0f230d169b310f328ffd016#rd',
                        ],
                        [
                            'type' => 'view',
                            'name' => '用户案例',
                            'url' => 'https://mp.weixin.qq.com/s?__biz=Mzg2NDc3MTQyOA==&mid=2247483688&idx=1&sn=92432dd799cb40f5809f188e3c4d50ef&chksm=ce650d23f9128435b72f2b0041928e3319a7f8faaab8ab868ec09428a4bdc2e72a06bfd38d41#rd',
                        ],
                    ],
                ],
                [
                    'type' => 'view',
                    'name' => '健康知识',
                    'url' => "https://mp.weixin.qq.com/mp/homepage?__biz=MzA5OTQwMzc4MQ==&hid=1&sn=57811442867c5c869ac84fb3922c8906&scene=18",
                ],
                [
                    'type' => 'click',
                    'name' => '在线客服',
                    'key' => "ONLINE_SERVICE",
                ]
            ]
            // 'button' => [
            //     [
            //         'type' => 'view',
            //         'name' => '我的报告',
            //         'url' => $url,
            //     ],
            //     // [
            //     //     'name' => '叶黄素',
            //     //     'type' => 'view',
            //     //     'url' => EYE_DOMAIN_HTTPS_PE . 'h5-v2/xanthophyll/index?source=h5&type=4',
            //     // ],
            //     // [
            //     //     'name' => '爱眼日专场',
            //     //     'type' => 'view',
            //     //     'url' => 'https://shop42585899.m.youzan.com/wscshop/showcase/feature?alias=4QKXDJnbUk&kdt_id=42393731',
            //     // ],
            //     // [
            //     //     'type' => 'click',
            //     //     'name' => '报告解读',
            //     //     'key' => "HELLO_AIRDOC",
            //     // ],
            // ]
        ];
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function createMenu()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 0);
        $data = [
            'button' => [
                [
                    'type' => 'view',
                    'name' => '我的报告',
                    'url' => $url,
                ],
                // [
                //     'name' => '叶黄素',
                //     'type' => 'view',
                //     'url' => EYE_DOMAIN_HTTPS_PE . 'h5-v2/xanthophyll/index?source=h5&type=4',
                // ],
                [
                    'name' => '爱眼日专场',
                    'type' => 'view',
                    'url' => 'https://shop42585899.m.youzan.com/wscshop/showcase/feature?alias=4QKXDJnbUk&kdt_id=42393731',
                ],
                [
                    'type' => 'click',
                    'name' => '报告解读',
                    'key' => "HELLO_AIRDOC",
                ],
            ]
        ];
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function createMenuZhongyou()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 3);
        $data = [
            'button' => [
                [
                    'type' => 'view',
                    'name' => '我的报告',
                    'url' => $url,
                ],
                // [
                //     'type' => 'view',
                //     'name' => '产品介绍',
                //     'url' => EYE_DOMAIN_HTTPS_PE . 'h5/miniprogram/twoMinute?frm=wcadc',
                // ],
                [
                    'type' => 'click',
                    'name' => '报告咨询',
                    'key' => "HELLO_AIRDOC",
                ],
            ]
        ];
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function createConditionalMenuZhongyou()
    {
        $access_token = $this->getBaseAccessToken();
        $url = $this->getRedirectUrl('', 3);
        $data = [
            'button' => [
                [
                    'type' => 'view',
                    'name' => '我的报告',
                    'url' => $url,
                ],
                [
                    'type' => 'click',
                    'name' => '青岛眼科',
                    'key' => "QINGDAO_AIRDOC",
                ],
            ],
            "matchrule" => [
                "tag_id" => "100",
            ]
        ];
        $url = sprintf($this->createConditionalMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function createMenuICVD()
    {
        $access_token = $this->getBaseAccessToken();
        // $url = $this->getRedirectUrl('', 3);
        $data = [
            'button' => [
                [
                    'name' => '公司介绍',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '公司介绍',
                            'url' => 'https://img3.airdoc.com/staticResources/agent/static/js/0kfFVPxwy9.pdf'
                            //'url' => 'https://mp.weixin.qq.com/s?__biz=MzA5OTQwMzc4MQ==&mid=2247484187&idx=1&sn=f0667ed3365455367f59d587915ca79c&chksm=90839271a7f41b67928abf42775c18d2a0b3333a2251ef7feb63f5cc489ba39e96ca29f27101#rd',
                        ],
                        [
                            'type' => 'view',
                            'name' => '医学原理',
                            'url' => 'https://mp.weixin.qq.com/s?__biz=MzA5OTQwMzc4MQ==&mid=2247484167&idx=1&sn=c97aa51071882233d55143fa8e2cf210&chksm=9083926da7f41b7bcd5e3c5303bea5749bf820488729f74adcfb1c10d5e374752b75342f607b#rd',
                        ],
                        [
                            'type' => 'view',
                            'name' => '产品介绍',
                            'url' => 'https://mp.weixin.qq.com/s?__biz=MzA5OTQwMzc4MQ==&mid=2247484511&idx=1&sn=3511c8243bd12131790939e434ba382b&chksm=90839535a7f41c23defaf83fbe967c0362fafccb847f88ae398763f2fb4a79ed2a5f1eaf5929#rd',
                        ],
                        [
                            'type' => 'view',
                            'name' => '用户案例',
                            'url' => 'https://mp.weixin.qq.com/s?__biz=MzA5OTQwMzc4MQ==&mid=2247484189&idx=1&sn=b93a1946635dcac15d8404d48dd28839&chksm=90839277a7f41b611f3579df7574d64a5886070c5f7ad14f64a3c4a405a642b8c1c13fc44e9e#rd',
                        ],
                    ],
                ],
                [
                    'type' => 'view',
                    'name' => '健康知识',
                    'url' => "https://mp.weixin.qq.com/mp/homepage?__biz=MzA5OTQwMzc4MQ==&hid=1&sn=57811442867c5c869ac84fb3922c8906",
                ],
                [
                    'name' => '联系我们',
                    'type' => 'click',
                    'key' => "ONLINE_SERVICE",
                    // 'sub_button' => [
                    //     [
                    //         'type' => 'click',
                    //         'name' => '解读报告',
                    //         'key' => "REPORT_CUSTOMER_SERVICE",
                    //     ],
                    //     [
                    //         'type' => 'click',
                    //         'name' => '在线客服',
                    //         'key' => "ONLINE_SERVICE",
                    //     ],
                    // ]
                ]
            ]
            // 'button' => [
            //     [
            //         'type' => 'click',
            //         'name' => '产品介绍',
            //         'key' => "AIRDOC_PRODUCT",
            //     ],
            //     [
            //         'type' => 'view',
            //         'name' => '公司介绍',
            //         'url' => "http://mp.weixin.qq.com/mp/video?__biz=MzIwMjM3NjQwMg==&mid=100002101&sn=f1ea81456630c8f68ea8c50dccb923cb&vid=wxv_2038651772994420737&idx=1&vidsn=50be6c9d50f3c348fde2c9ee809eec0a&fromid=1&scene=18&xtrack=1#wechat_redirect",
            //     ],
            //     [
            //         'type' => 'click',
            //         'name' => '解读服务',
            //         'key' => "REPORT_CUSTOMER_SERVICE",
            //     ],
            // ],
        ];
        $url = sprintf($this->createMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }
    public function createConditionalMenuICVD()
    {
        $access_token = $this->getBaseAccessToken();
        // $url = $this->getRedirectUrl('', 3);
        $url = 'https://test-h5.wlbb.net/h5/my/userCenter';
        if (ENV != 'test') {
            $url = 'https://h5.futurebaobei.com/h5/my/userCenter';
        }
        $data = [

            'button' => [
                [
                    'name' => '我的问诊',
                    'type' => 'view',
                    'url' => $url,
                ],
            ],
            "matchrule" => [
                "tag_id" => "100",
            ]
        ];
        $url = sprintf($this->createConditionalMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function getConditionalMenuICVD($openid)
    {
        $access_token = $this->getBaseAccessToken();
        // $url = $this->getRedirectUrl('', 3);
        $data = ['user_id' => $openid];
        $url = sprintf($this->getConditionalMenuUrl, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function getUserTags($openid)
    {
        $access_token = $this->getBaseAccessToken();
        // $url = $this->getRedirectUrl('', 3);
        $data = ['openid' => $openid];
        $url = sprintf($this->getUserTags, $access_token);
        return self::curl($url, $data, 1, 1);
    }

    public function createQRCode($check_id, $r = 0)
    {
        $ckey = $this->appId . '_' . $check_id;
        $cache = RedisCheckQRCode::getCache($ckey . '_url');
        if ($cache) {
            $cache = json_decode($cache, 1);
            if ($cache['url']) {
                $qrcode_qiniu_url_signed = RedisImageUrl::signedUrl($cache['url']);
                return [$qrcode_qiniu_url_signed, $cache['expiry_date']];
            }
        }
        // BAEQ-3800 缓存七牛图片路径
        // $ticket = RedisCheckQRCode::getCache($ckey);
        // if ($ticket) {
        //     $ttl = RedisCheckQRCode::getTTL($ckey);
        //     $pass_time = RedisCheckQRCode::EXPIRE_TIME - $ttl;
        //     $create_time = time() - $pass_time;
        //     $expiry_date = date("Y-m-d H:i:s", $create_time + 2592000);
        //     return ['https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket), $expiry_date];
        // }
        $expiry_date = date("Y-m-d H:i:s", time() + 2592000);
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createQRCodeUrl, $access_token);
        $scene_key = 'scene_id';
        $action_name = 'QR_SCENE';
        if ($this->is_ant || strpos($check_id, 'en_US')) {
            $scene_key = 'scene_str';
            $action_name = 'QR_STR_SCENE';
        }
        $data = [
            'action_name' => $action_name,
            'expire_seconds' => 2592000,
            'action_info' => ['scene' => [$scene_key => $check_id]]
        ];
        $result = self::curl($url, $data, 1, 1, 'https');
        if (!isset($result['ticket']) && !$r) {
            list($result, $expiry_date) = $this->createQRCode($check_id, 1);
        }
        $ticket = $result['ticket'];
        if ($ticket) {
            // RedisCheckQRCode::setCache($ckey, $ticket);
            $wechat_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
            $qrcode_qiniu_url = Image::uploadImageByUrl($wechat_url, 1, 'checkinfo/qrcode/' . md5($wechat_url) . date('ymdHis') . '.jpg');
            if (is_array($qrcode_qiniu_url) && $qrcode_qiniu_url['url']) {
                $qrcode_qiniu_url = $qrcode_qiniu_url['url'];
            }
            $qrcode_qiniu_url_signed = RedisImageUrl::signedUrl($qrcode_qiniu_url);
            RedisCheckQRCode::setCache($ckey . '_url', json_encode(['url' => $qrcode_qiniu_url, 'expiry_date' => $expiry_date]));
            return [$qrcode_qiniu_url_signed, $expiry_date];
        }
        if (ENV == "production") {
            $sms_data = ['phone' => '13811885439', 'content' => 'AK生成二维码失败-北京爱康' . $check_id];
            if (WX_APPID == $this->appId) {
                $sms_data = ['phone' => '13811885439', 'content' => 'AK生成二维码失败-慧心瞳' . $check_id];
            }
            // \Air\Package\User\Sms::smsRecord($sms_data);
            if (time() % 60 == 1) {
                Util::DDMonitor('P3-' . $sms_data['content'], 'cloudm');
            }
        }
        return ['', ''];
    }

    public function createQRCodeByUuid($uuid, $r = 0, $code_prefix = 'uuid_')
    {
        $ticket = RedisCheckQRCode::getCache($uuid);
        if ($ticket) {
            return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
        }
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createQRCodeUrl, $access_token);
        $scene_key = 'scene_str';
        $action_name = 'QR_STR_SCENE';
        $data = [
            'action_name' => $action_name,
            'expire_seconds' => 2592000,
            'action_info' => ['scene' => [$scene_key => $code_prefix . $uuid]]
        ];
        $result = self::curl($url, $data, 1, 1, 'https');
        if (!isset($result['ticket']) && !$r) {
            \Phplib\Tools\Logger::error([$data, $result], 'wechat_send_msg_error');
            $this->createQRCodeByUuid($uuid, 1, $code_prefix);
        }
        $ticket = $result['ticket'];
        RedisCheckQRCode::setCache($uuid, $ticket);
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
    }

    public function createQRCodeByYWCheckId($check_id)
    {
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createQRCodeUrl, $access_token);
        $scene_key = 'scene_str';
        $action_name = 'QR_STR_SCENE';
        $data = [
            'action_name' => $action_name,
            'expire_seconds' => 2592000,
            'action_info' => ['scene' => [$scene_key => 'YTSM_' . $check_id]]
        ];
        $result = self::curl($url, $data, 1, 1, 'https');
        if (!isset($result['ticket'])) {
            return "";
        }
        $ticket = $result['ticket'];

        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
    }

    public function createQRLimitStrScene($prefix, $key = '')
    {
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createQRCodeUrl, $access_token);
        $scene_key = 'scene_str';
        $action_name = 'QR_LIMIT_STR_SCENE';
        $scene_str = $prefix . $key;
        $data = [
            'action_name' => $action_name,
            'action_info' => ['scene' => [$scene_key => $scene_str]]
        ];

        $result = self::curl($url, $data, 1, 1, 'https');
        if (!isset($result['ticket'])) {
            return "";
        }
        $ticket = $result['ticket'];
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
    }
    public function createQRLimitStrSceneCopy($prefix, $key = '')
    {
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createQRCodeUrl, $access_token);
        $scene_key = 'scene_str';
        $action_name = 'QR_LIMIT_STR_SCENE';
        $scene_str =  $key . $prefix;
        $data = [
            'action_name' => $action_name,
            'action_info' => ['scene' => [$scene_key => $scene_str]]
        ];

        $result = self::curl($url, $data, 1, 1, 'https');
        if (!isset($result['ticket'])) {
            return "";
        }
        $ticket = $result['ticket'];
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($ticket);
    }

    public function createRefQRCode($check_id)
    {
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createQRCodeUrl, $access_token);
        $check_info = DBCheckInfoHelper::getLines(['id' => intval($check_id)], true);
        $org_id = $check_info[0]['org_id'];
        $data = [
            'action_name' => 'QR_STR_SCENE',
            'expire_seconds' => 2592000,
            'action_info' => ['scene' => ['scene_str' => 'ref_' . $check_id]]
        ];
        $result = self::curl($url, $data, 1, 1, 'https');
        if (!isset($result['ticket'])) {
            $this->createRefQRCode($check_id);
        }
        $ticket = $result['ticket'];
        RedisCheckQRCode::setCache('ref_' . $check_id, $ticket);
        return [$ticket, $org_id];
    }

    public function changeOpenid($from_appid, $openid_list)
    {
        $access_token = $this->getBaseAccessToken();
        $data = ['from_appid' => $from_appid, 'openid_list' => $openid_list];
        $url = sprintf($this->change_openid_url, $access_token);
        $result = self::curl($url, $data, 1, 1, 'https');
        \Phplib\Tools\Logger::error([$data, $result], 'change_openid_curl');
        return $result;
    }

    public function makeTag4User($tagid, $openid)
    {
        $access_token = $this->getBaseAccessToken();
        $data = ['tagid' => $tagid, 'openid_list' => [$openid]];
        $url = sprintf($this->makeTagsUrl, $access_token);
        $result = self::curl($url, $data, 1, 1, 'https');
        \Phplib\Tools\Logger::error([$data, $result], 'wechat_make_tag');
        return $result;
    }

    public function createTag($tag_name)
    {
        $access_token = $this->getBaseAccessToken();
        $data = ['tag' => ['name' => $tag_name]];
        $url = sprintf($this->createTagsUrl, $access_token);
        self::curl($url, $data, 1, 1, 'https');
    }

    public function createTags()
    {
        $result = [];
        foreach ($this->wechatTags as $tag) {
            $access_token = $this->getBaseAccessToken();
            $data = ['tag' => ['name' => $tag]];
            $url = sprintf($this->createTagsUrl, $access_token);
            $result[$tag] = self::curl($url, $data, 1, 1, 'https');
            $this->delSetting('wechat_tags');
        }
        return $result;
    }

    public function getTags()
    {
        $key = 'wechat_tags';
        $tags = $this->getSetting($key);
        if ($tags) {
            $tags['cache'] = 1;
            return $tags;
        }
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->getTagsUrl, $access_token);
        $result = self::curl($url);
        $ret = $this->addSetting(['value' => $result, 'key' => $key, 'expires_time' => 86400]);
        return $result;
    }

    public function saveTags()
    {
        $tags = $this->getTags();
        $wechatTag = new WechatTag();
        foreach ($tags['tags'] as $tag) {
            $wechatTag->addTag($tag);
        }
    }

    public static function getBase64Image($url)
    {
        $image_data = file_get_contents($url, 'r');
        return 'data:image/jpeg;base64,' . base64_encode($image_data);
    }

    public function createChannelQRCode($channel_num)
    {
        $access_token = $this->getBaseAccessToken();
        $url = sprintf($this->createQRCodeUrl, $access_token);
        $data = [
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_str' => 'channel_' . $channel_num
                ]
            ]
        ];
        $result = self::curl($url, $data, 1, 1, 'https');
        if (!isset($result['ticket'])) {
            return $this->createChannelQRCode($channel_num);
        }
        return $ticket = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($result['ticket']);
    }

    public function uploadPermanentMedia($filename)
    {
        $access_token = $this->getBaseAccessToken();
        $upload_media_url = sprintf($this->uploadpermanentImageMediaUrl, $access_token, 'image');
        $file = new \CURLFile($filename, 'image/png', basename($filename));
        $post_data = ['media' => $file];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_media_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $res = json_decode($result, TRUE);

        return $res['media_id'];
    }

    public function uploadImageMedia($filename)
    {
        $media_id = $this->getSetting($filename);
        if ($media_id) {
            return $media_id;
        }
        $access_token = $this->getBaseAccessToken();
        $upload_media_url = sprintf($this->uploadMediaUrl, $access_token, 'image');
        $file = new \CURLFile($filename, 'image/png', basename($filename));
        $post_data = ['media' => $file];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $upload_media_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $res = json_decode($result, TRUE);

        if (isset($res['errcode'])) {
            return FALSE;
        }
        $this->addSetting(array('key' => $filename, 'value' => $res['media_id'], 'expires_time' => 87000));
        return $res['media_id'];
    }

    /**
     * $is_third 1: 圆和; 2: 中翔
     */
    public static function generateScreenImage($in_file, $code, $out_file, $push_type = 0, $is_third = 0, $is_yw = 0, $is_ytsm = 0, $is_fy = 0, $is_tzj = 0, $is_yt_health = 0)
    {
        $pc = \Air\Package\User\PatientCode::getItemByPcode($code);
        $template_img_file = __DIR__ . '/../../config/assets/template.png';
        putenv('GDFONTPATH=/usr/share/fonts/yahei/');
        $font = 'Yaheib.ttf';
        $str = $in_file['name'] ? mb_convert_encoding(mb_substr($in_file['name'], 0, 5) . ' ' . $code, "html-entities", "utf-8") : $code;
        if ($is_third === 1 || $is_yw || $is_ytsm) {
            $template_img_file = __DIR__ . '/../../config/assets/template_yuanhe.png';
            $is_ytsm && $template_img_file = __DIR__ . '/../../config/assets/template_ytsm.png';
            $is_yw && $template_img_file = __DIR__ . '/../../config/assets/template_yw.png';
            $is_yw > 1 && $template_img_file = __DIR__ . '/../../config/assets/template_fg.png';
            $img_template = imagecreatefrompng($template_img_file);
            $img_barcode = imagecreatefrompng($in_file['barcode']);
            $img_qrcode = imagecreatefrompng($in_file['qrcode']);
            $img_resized_barcode = imagecreatetruecolor(400, 200);
            $img_resized_qrcode = imagecreatetruecolor(400, 400);
            imagecopyresized($img_resized_qrcode, $img_qrcode, 0, 0, 0, 0, 400, 400, 200, 200);
            imagecopymerge($img_template, $img_resized_qrcode, 321, 187, 0, 0, 400, 400, 100);
            imagecopyresized($img_resized_barcode, $img_barcode, 0, 0, 0, 0, 445, 400, 200, 200);
            imagecopymerge($img_template, $img_resized_barcode, 321, 647, 0, 0, 400, 200, 100);
            if ($is_ytsm) {
                imagettftext($img_template, 40, 0, 230, 1020, 0x13CBC0, $font, $str);
            } else {
                imagettftext($img_template, 40, 0, 230, 1020, 0xE7497C, $font, $str);
            }
            imagepng($img_template, $out_file);
            return;
        }
        $is_third === 2 && $template_img_file = ROOT_PATH . '/config/assets/template_zx.png';
        $is_fy && $template_img_file = ROOT_PATH . '/config/assets/template_fy.png';
        $is_tzj && $template_img_file = __DIR__ . '/../../config/assets/template_tzj.png';
        $is_yt_health && $template_img_file = __DIR__ . '/../../config/assets/template_tzj.png';
        $img_template = imagecreatefrompng($template_img_file);
        $img_barcode = imagecreatefrompng($in_file['barcode']);
        $img_qrcode = imagecreatefrompng($in_file['qrcode']);
        $img_resized_barcode = imagecreatetruecolor(400, 200);
        $img_resized_qrcode = imagecreatetruecolor(400, 400);
        imagecopyresized($img_resized_qrcode, $img_qrcode, 0, 0, 0, 0, 350, 350, 200, 200);
        imagecopymerge($img_template, $img_resized_qrcode, 206, 166, 0, 0, 350, 350, 100);
        imagecopyresized($img_resized_barcode, $img_barcode, 0, 0, 0, 0, 335, 300, 200, 200);
        imagecopymerge($img_template, $img_resized_barcode, 230, 507, 0, 0, 300, 150, 100);
        $left = 90;
        if (strlen($in_file['name']) > mb_strlen($in_file['name']) * 2) {
            $words = mb_strlen($in_file['name']);
            if ($words < 4) {
                $left = 90 + (4 - $words) * 15;
            }
        }
        imagettftext($img_template, 40, 0, $left, 790, 0xE7497C, $font, $str);
        imagepng($img_template, $out_file);
    }

    public static function h5Url2Miniprogram($url)
    {
        $url_info = parse_url($url);
        $mark = '';
        if (strpos($url, '/api/wechat/jump') && strpos($url, 'jump_to_payment=1')) {
            $mark = '-payment';
        } elseif (strpos($url, '/api/wechat/jump') && !strpos($url, 'is_fd16=1')) {
            $mark = '-big';
        } elseif (strpos($url, '/api/wechat/jump') && strpos($url, 'register_type=0')) {
            $mark = '-postposition';
        }
        $url_path = $url_info['path'] . $mark . '?' . $url_info['query'];
        $arr = array(
            '/fd16/start?' => 'pages/startFD16/startFD16?', // 相机启动页面
            '/landing/payment?' => 'pages/payment/payment?', // wei支付
            '/api/wechat/jump?' => 'pages/fullUserInfoSet/fullUserInfoSet?', // 慧心瞳众佑小相机jump登记
            '/api/wechat/jump-payment?' => 'pages/payment/payment?', // 慧心瞳众佑小相机jump支付页面
            '/api/wechat/jump-postposition?' => 'pages/fd16PhoneVision/fd16PhoneVision?', // 慧心瞳众佑小相机jump后登记
            '/api/wechat/jump-big?' => 'pages/bigCameraUserInfo/bigCameraUserInfo?', // 慧心瞳大相机jump登记
            '/fd16/fulluserinfo/set?' => 'pages/fullUserInfoSet/fullUserInfoSet?', // 鹰瞳小相机登记
            '/fd16/userinfo/set?' => 'pages/fd16PhoneVision/fd16PhoneVision?', // 小相机后补充信息，获取筛查码
            '/userinfo/set?' => 'pages/bigCameraUserInfo/bigCameraUserInfo?', // 鹰瞳大相机登记
            '/icvd/register?' => 'pages/bigCameraPhone/bigCameraPhone?', // 大相机后补充信息，获取筛查码
            '/api/wechat/jump-sat' => '/pages/fullUserInfoSet/fullUserInfoSet', //CHP辅助登记筛查页面
            '/api/wechat/payment' => 'pages/payAndCheck/payAndCheck', //微信支付
        );
        return strtr($url_path, $arr);
    }
    //判断用户扫描的二维码和出的报告是否一致
    static public function qrMatchReport($customer_id, $type)
    {
        $customer = Organizer::getAllCustomer();
        if ($customer_id == 0) {
            return ['error_code' => true];
        } elseif ($customer_id == 41) {
            return ['error_code' => false];
        } elseif (empty($customer[$customer_id]['type'])) {
            //拓客体验 不需要判断
            return ['error_code' => true];
        } elseif ($customer[$customer_id]['type'] == $type) {
            return ['error_code' => true];
        }
        return ['error_code' => false, 'org_report_name' => self::REPORT_TYPE_NAME[$customer[$customer_id]['type']], 'qr_report_name' => self::REPORT_TYPE_NAME[$type], 'report_type' => $customer[$customer_id]['type']];
    }
    //使用第三方公众号启动相机
    static public function thirdQrMatchReport($org_id, $type)
    {
        if (in_array($org_id, self::THIRDS_ORG_IDS[$type])) {
            return true;
        }
        return false;
    }
}
