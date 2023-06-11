<?php
namespace Air\Modules\Wechat;

use Air\Package\Checklist\Helper\RedisImageUrl;
use Air\Package\Wechat\WechatMiniProgram;
use Air\Package\Wechat\WechatScene;

class Get_wxacode extends \Air\Libs\Controller
{

    public $must_login = TRUE;

    public function run()
    {
        $request = $this->request->REQUEST;
        $must = ['pcode', 'sn', 'key', 'token'];
        $other_params = [];
        $params = [];
        $extra = [];
        foreach ($must as $key) {
            if (!isset($request[$key])) {
                $this->setView(100001, '缺少参数' . $key, '');
                return false;
            }
            if (!trim($request[$key])) {
                $this->setView(100002, '参数' . $key . '为空', '');
                return false;
            }
        }
        foreach ($request as $key => $item) {
            if (in_array($key, $must) || in_array($key, $other_params)) {
                $params[$key] = trim($item);
            } else {
                $extra[$key] = trim($item);
            }
        }
        $scene = WechatScene::getScene($params);
        $item = WechatScene::getItem($scene);
        if ($item && $item['qrcode_url']) {
            $qrcode_url = RedisImageUrl::signedUrl($item['qrcode_url']);
            $this->setView(0, 'success', ['qrcode_url' => $qrcode_url]);
            return TRUE;
        }
        $qrcode_url = WechatMiniProgram::getUnlimitedWxacode($scene, 'pages/startScanner/startScanner');
        if ($qrcode_url) {
            $ret = WechatScene::addItem($params, $qrcode_url);
            $qrcode_url = RedisImageUrl::signedUrl($qrcode_url);
            $this->setView(0, 'success', ['qrcode_url' => $qrcode_url]);
            return TRUE;
        }
        $this->setView(100009, 'failed', '');
        return TRUE;
    }
}
