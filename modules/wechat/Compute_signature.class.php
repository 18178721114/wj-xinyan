<?php

namespace Air\Modules\Wechat;

use Air\Package\Wechat\WXUtil;

class Compute_signature extends \Air\Libs\Controller
{
    public function run()
    {
        $url = \Air\Libs\Base\Utilities::decodeAmp($this->request->REQUEST['url']);
        \Phplib\Tools\Logger::error($url, 'config_init_url');
        // step1: get the access_token
        // step2: get the jsapi_ticket
        if (isset($this->request->REQUEST['type']) && $this->request->REQUEST['type'] == 'yt_health') {
            $appid = YTHEALTH_WX_APPID;
            $wx_util = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
        } elseif (isset($this->request->REQUEST['type']) && $this->request->REQUEST['type'] == 'tzj') {
            $appid = TZJ_WX_APPID;
            $wx_util = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
        } elseif (ENV == 'test') {
            $appid = WX_APPID;
            $wx_util = new WXUtil($appid, WX_SECRET);
        } else {
            $appid = WX_APPID_NEW;
            $wx_util = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
        }
        $jsapi_ticket = $wx_util->getTicket();
        $timestamp = time();
        $nonceStr = uniqid();
        // step3: compute the signature
        $signature = $this->computeSignature($jsapi_ticket, $nonceStr, $timestamp, $url);
        $result = [
            'appId' => $appid,
            'timestamp' => $timestamp,
            'nonceStr' => $nonceStr,
            'signature' => $signature,
            'jsapi_ticket' => $jsapi_ticket,
            'url' => $url,
        ];
        $this->setView(0, 'ok', $result);
    }

    private function _init()
    {
        if (!isset($this->request->REQUEST['url']) || empty($this->request->REQUEST['url'])) {
            $this->setView(10001, 'missing url parameter', []);
            return FALSE;
        }
        return TRUE;
    }

    private function computeSignature($jsapi_ticket, $nonceStr, $timestamp, $url)
    {
        $str = 'jsapi_ticket=' . $jsapi_ticket
            . '&noncestr=' . $nonceStr
            . '&timestamp=' . $timestamp
            . '&url=' . $url;
        return sha1($str);
    }
}
