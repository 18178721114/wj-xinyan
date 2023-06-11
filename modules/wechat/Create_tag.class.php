<?php
/**
 * Date: 2017/10/13
 * Time: 下午4:57
 */
namespace Air\Modules\Wechat;

use Air\Libs\Base\Utilities;
use Air\Package\Wechat\WXUtil;

class Create_tag extends \Air\Libs\Controller
{
    const ALLOW_IPS = [
        '101.200.85.230',
        '59.110.49.59',
        '36.112.64.2',
        '39.107.84.77',
        '116.247.81.186',
        '123.57.216.175', // 小瞳助手测试环境
    ];

    public function run()
    {
        $ip = Utilities::getClientIP('string');
        if (!in_array($ip, self::ALLOW_IPS)) {
            $this->setView(0, 'ip不在白名单中', '');
            return false;
        }

        if (!$this->_init()) {
            return false;
        }
        $product = trim($this->request->REQUEST['product']);
        $tag = trim($this->request->REQUEST['tag']);
        if ($product == 'yt') {
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        } else if ($product == 'zy') {
            $obj = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
        } else if ($product == 'hxt') {
            $obj = new WXUtil(WX_APPID, WX_SECRET);
        } else {
            $this->setView(10050, '不支持此公众号', '');
            return false;
        }
        $obj->createTag($tag);
        $tags = $obj->getTags();
        $this->setView(0, '', $tags);
    }

    private function _init() {
        if (empty($this->request->REQUEST['product'])) {
            $this->setView(10040, '缺少公众号名称', '');
            return false;
        }

        if (empty($this->request->REQUEST['tag'])) {
            $this->setView(10040, '缺少tag名', '');
            return false;
        }

        return true;
    }
}
