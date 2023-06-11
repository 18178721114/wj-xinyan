<?php

namespace Air\Package\Wechat;

use Air\Libs\Xcrypt;
use \Air\Package\Cache\RedisCache;
use Air\Package\Checklist\Image;
use Air\Package\Checklist\QiniuHandler;
use Phplib\Tools\Logger;

class WechatMiniProgram
{
    const SESSION_PREFIX = 'REGISTER_USER_';
    const WX_SESSION_KEY = 'WX_SESSION_KEY_';

    const OK = 0;
    const IllegalAesKey = -41001;
    const IllegalIv = -41002;
    const IllegalBuffer = -41003;
    const DecodeBase64Error = -41004;
    const WECHAT_PLATFORM = [
        'sat' => [
            'appid' => SAT_WX_APPID,
            'secret' => SAT_WX_SECRET
        ],
        'common' => [
            'appid' => REGISTER_WX_APPID,
            'secret' => REGISTER_WX_SECRET
        ]
    ];

    public static function getUnlimitedWxacode($scene, $page) {
        $wx_util = new WXUtil(REGISTER_WX_APPID, REGISTER_WX_SECRET);
        $access_token = $wx_util->getBaseAccessToken();
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;
        $params = [
            'scene' => $scene,
            'page' => $page,
            'width' => 430,
        ];
        $result = WXUtil::curl($url, $params, 1, 1, 'http', 1);
        if ($result) {
            //zj $ret = QiniuHandler::uploadImageByBinary([$result], 'wxacode_scene/')[0];
            $ret = Image::uploadImageByBinary([$result], 'wxacode_scene/')[0];
            Logger::info('scene: ' . $scene . 'url: ' . $ret, 'wxacode_scene');
            return $ret;
        }
        Logger::info('scene: ' . $scene . ' failed', 'wxacode_scene');
        return false;
    }

    public static function getAuth($code, $platform = 'common')
    {
        $appid = self::WECHAT_PLATFORM[$platform]['appid'];
        $secret = self::WECHAT_PLATFORM[$platform]['secret'];
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
        $content = file_get_contents($url);
        $auth = json_decode($content, true);
        return $auth;
    }

    public static function getSessionKey($token)
    {
        $key = RedisCache::getCache($token, self::WX_SESSION_KEY);

        return $key;
    }

    public static function saveSessionKey($token, $session_key)
    {
        RedisCache::setCache($token, $session_key, self::WX_SESSION_KEY, 3600);
    }

    public static function saveAppSession($token, $user)
    {  
        RedisCache::setCache($token, serialize($user), self::SESSION_PREFIX, 86400 * 60);
    }

    public static function getAppSession($token)
    {
        $str = RedisCache::getCache($token, self::SESSION_PREFIX);

        return unserialize($str);
    }

    public static function clearAppSession($token)
    {
        return RedisCache::delCache($token, self::SESSION_PREFIX);
    }

    /**
    * 检验数据的真实性，并且获取解密后的明文.
    * @param $encryptedData string 加密的用户数据
    * @param $iv string 与用户数据一同返回的初始向量
    * @param $data string 解密后的原文
    *
    * @return int 成功0，失败返回对应的错误码
    */
    public static function decryptData($appid, $sessionKey, $encryptedData, $iv, &$data)
    {
        \Phplib\Tools\Logger::error(['decryptData_start', $appid, $sessionKey, $encryptedData, $iv], 'miniprogram');
        if (strlen($sessionKey) != 24) {
            return self::IllegalAesKey;
        }
        $aesKey = base64_decode($sessionKey);
        if (strlen($iv) != 24) {
            return self::IllegalIv;
        }
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == NULL) {
            return self::IllegalBuffer;
        }
        if ($dataObj->watermark->appid != $appid) {
            return self::IllegalBuffer;
        }
        $data = json_decode($result);
        $data->encryptData = Xcrypt::encrypt($data->phoneNumber);
        \Phplib\Tools\Logger::error(['decryptData_end', $data, $encryptedData, $iv], 'miniprogram');
    }
}