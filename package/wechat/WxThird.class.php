<?php
/**
 * Created by PhpStorm.
 * User: shanshizhong
 * Date: 2019/7/11
 * Time: 15:19
 */

namespace Air\Package\Wechat;

use Phplib\Tools\Logger;
use Air\Package\Wechat\Helper\DBWxThirdHelper;
use Air\Package\Wechat\Helper\RedisWechat;
use Air\Libs\Base\Utilities;

class WxThird
{
    const KEY_COMPONENT_ACCESS_TOKEN = "get_wxkf_component_access_token";
    const GET_PRE_AUTH_CODE_URL = "https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=%s";//请求pre_auth_code
    const GET_REDIRECT_BACK_URL = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s";
    const GET_REPORT_LIST = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=http://optometry.airdoc.com/api/wxkf/AuthJumpUrl&response_type=code&scope=snsapi_base&state=jump_report&component_appid=%s#wechat_redirect";//获取报告列表
    const GET_OAUTH = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&code=%s&grant_type=authorization_code&component_appid=%s&component_access_token=%s";//获取用户信息
    const GET_QUERY_AUTH = "https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=%s";//查看公众号appid
    const GET_AUTHORIZER_INFO = "https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=%s";//查看公众号账号详情
    const GET_COMPONENT_TOKEN = "https://api.weixin.qq.com/cgi-bin/component/api_component_token";//获取令牌（component_access_token）
    const OTHER_AUTHORIZER_TOKEN = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=%s";//获取其他公众号的access token
    const SEND_CUSTOM_MESSAGE = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s";//小程序发送客服消息
    const TEMPLATE_SOFTWARE_List = ['yuyue' => 'OPENTM417735716', 'tongzhi' => 'OPENTM414852429'];
    const TEMPLATE_INTER_List = ['yuyue' => 'OPENTM401887092', 'tongzhi' => 'OPENTM408263603'];

    const HANGYELIST = [
        1 => "互联网|电子商务",
        2 => "IT软件与服务",
        3 => "IT硬件与设备",
        4 => "电子技术",
        5 => "通信与运营商",
        6 => "网络游戏",
        7 => "银行",
        8 => "基金理财信托",
        9 => "保险",
        10 => "餐饮",
        11 => "酒店",
        12 => "旅游",
        13 => "快递",
        14 => "物流",
        15 => "仓储",
        16 => "培训",
        17 => "院校",
        18 => "学术科研",
        19 => "交警",
        20 => "博物馆",
        21 => "公共事业非盈利机构",
        22 => "医药医疗",
        23 > "护理美容",
        24 => "保健与卫生",
        25 => "汽车相关",
        26 => "摩托车相关",
        27 => "火车相关",
        28 => "飞机相关",
        29 => "建筑",
        30 => "物业",
        31 => "消费品",
        32 => "法律",
        33 => "会展",
        34 => "中介服务",
        35 => "认证",
        36 => "审计",
        37 => "传媒",
        38 => "体育",
        39 => "娱乐休闲",
        40 => "印刷",
        41 => "其它"
    ];

    //获取过期的公众号列表
    public static function getExpireTimeList($time)
    {
        $sql = "select id,appid,name,access_token,refresh_token from ".DBWxThirdHelper::_TABLE_." where status=1 and expire_time<:expire_time";
        return DBWxThirdHelper::getDataBySql($sql, ['expire_time' => $time]);
    }

    public static function getInfoByAppid($appid, $columns = 'id,name,appid,access_token,template_id,template_report,refresh_token') {
        $sql = "select $columns from ".DBWxThirdHelper::_TABLE_." where appid=:appid and status=1;";
        $list = DBWxThirdHelper::getDataBySql($sql, ['appid' => $appid]);
        return !empty($list) ? $list[0] : [];
    }

    //获取开放平台的access token
    public static function getComponentAccessToken()
    {
        $comAccKey = self::KEY_COMPONENT_ACCESS_TOKEN;
        $component_access_token = RedisWechat::getCache($comAccKey);
        return $component_access_token;
    }

    //获取用户信息
    public static function getUserOauth($appid, $code)
    {
        $component_access_token = self::getComponentAccessToken();
        $url = sprintf(self::GET_OAUTH, $appid, $code, COMPONENT_APPID, $component_access_token);
        return Utilities::curl($url, array(), ['is_post' => 0]);
    }

    //获取账号权限
    public static function getQueryAuth($auth_code)
    {
        $component_access_token = self::getComponentAccessToken();
        $url = sprintf(self::GET_QUERY_AUTH, $component_access_token);
        $postData["component_appid"] = COMPONENT_APPID;
        $postData["authorization_code"] = $auth_code;
        return Utilities::curl($url, $postData, ['is_post' => 1, 'is_json' => 1]);
    }

    //查看公众号账号详情
    public static function getWxAccountInfo($authorizer_appid)
    {
        $component_access_token = self::getComponentAccessToken();
        $url = sprintf(self::GET_AUTHORIZER_INFO, $component_access_token);
        $postData["component_appid"] = COMPONENT_APPID;
        $postData["authorizer_appid"] = $authorizer_appid;
        $resData = Utilities::curl($url, $postData, ['is_post' => 1, 'is_json' => 1]);
        $return = [];
        if (isset($resData['authorizer_info'])) {
            $authorizer_info = $resData['authorizer_info'];
            $return['nick_name'] = $authorizer_info['nick_name'];
            $return['service_type_info'] = $authorizer_info['service_type_info']['id'];//公众号类型 0订阅号 2服务号
            $return['verify_type_info'] = $authorizer_info['verify_type_info']['id'];//微信认证 0微信认证 -1未认证
            $return['user_name'] = $authorizer_info['user_name'];//原始 ID
        }
        return $return;
    }

    //获取令牌（component_access_token）
    public static function getComponentToken($ComponentVerifyTicket)
    {
        //获取令牌（component_access_token）
        $url = self::GET_COMPONENT_TOKEN;
        $postData["component_appid"] = COMPONENT_APPID;
        $postData["component_appsecret"] = COMPONENT_APPSECRET;
        $postData["component_verify_ticket"] = $ComponentVerifyTicket;
        return Utilities::curl($url, $postData, ['is_post' => 1, 'is_json' => 1]);
    }

    public static function getOtherAuthorizerToken($component_access_token, $authorizer_appid, $refresh_token)
    {
        $url = sprintf(self::OTHER_AUTHORIZER_TOKEN, $component_access_token);
        $postData["component_access_token"] = $component_access_token;
        $postData["component_appid"] = COMPONENT_APPID;
        $postData["authorizer_appid"] = $authorizer_appid;
        $postData["authorizer_refresh_token"] = $refresh_token;
        return Utilities::curl($url, $postData, ['is_post' => 1, 'is_json' => 1]);
    }

    //新增
    public static function addInfo($appid, $access_token, $refresh_token, $expires_in, $name, $wx_id) {
        $data['appid'] = $appid;
        $data['access_token'] = $access_token;//access_token
        $data['refresh_token'] = $refresh_token;//refresh_token
        $data['expire_time'] = time() + $expires_in - 1800;//expire_time
        $data['name'] = $name;
        $data['wx_id'] = $wx_id;
        return DBWxThirdHelper::create($data);
    }

    //更新
    public static function updateInfo($appid, $access_token, $refresh_token, $expires_in)
    {
        $data['appid'] = $appid;//appid
        $data['access_token'] = $access_token;//access_token
        $data['refresh_token'] = $refresh_token;//refresh_token
        $data['expire_time'] = time() + $expires_in - 1800;//expire_time
        $updateSql = "update ".DBWxThirdHelper::_TABLE_." set access_token=:access_token,refresh_token=:refresh_token,expire_time=:expire_time,status=1 where appid=:appid;";
        return DBWxThirdHelper::updateDataBySql($updateSql, $data);
    }

}