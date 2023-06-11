<?php

/**
 * Created by PhpStorm.
 * User: qingyi
 * Date: 2017/10/13
 * Time: 下午4:57
 */

namespace Air\Modules\WeChat;

use Air\Package\Wechat\WechatThird;
use Air\Package\Wechat\WXUtil;

class Set_menu extends \Air\Libs\Controller
{
    public function run()
    {
        $allow_ip = ['36.112.64.2',];
        $ip = $this->request->ip;
        if (!in_array($ip, $allow_ip)) {
            \Air\Libs\Base\Utilities::DDMonitor('P3-' . $ip . ' - set menu attack', 'dev');
            $this->setView(0, '', []);
            return;
        }
        if ($this->request->REQUEST['appid']) {
            $wechat_config_data['appid'] = $this->request->REQUEST['appid'];
            $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
            if (!$wechat_config) {
                $this->setView(0, '未找到appid对应公众号', '');
                return false;
            }
            $appid = $wechat_config['appid'];
            $secret = $wechat_config['secret'];
            $obj = new WXUtil($appid, $secret);
            if ($this->request->REQUEST['is_delete']) {
                $result = $obj->thirdDeleteMenuNew();
            } elseif ($this->request->REQUEST['data']) {
                $data = $this->request->REQUEST['data'];
                $result = $obj->thirdCreateMenuNew($data);
            } else {
                $this->setView(0, 'data为空', '');
                return false;
            }
        } elseif ($this->request->REQUEST['yt_health'] || $this->request->REQUEST['yt_health']) {
            $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);

            if ($this->request->REQUEST['is_delete']) {
                $result = $obj->ytHealthDeleteMenuNew();
            } else {
                $result = $obj->ytHealthCreateMenuNew();
            }
        } elseif ($this->request->REQUEST['tizhijian'] || $this->request->REQUEST['tizhijian']) {
            $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);

            if ($this->request->REQUEST['is_delete']) {
                $result = $obj->tzjDeleteMenuNew();
            } else {
                $result = $obj->tzjCreateMenuNew();
            }
        } elseif ($this->request->REQUEST['is_new'] || $this->request->REQUEST['new']) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);

            if ($this->request->REQUEST['is_delete']) {
                $result = $obj->deleteMenuNew();
            } else {
                $result = $obj->createMenuNew();
            }
        } elseif ($this->request->REQUEST['icvd'] || $this->request->REQUEST['is_icvd']) {
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            if ($this->request->REQUEST['wlbb']) {
                $result = $obj->createConditionalMenuICVD();
            } else {
                $result = $obj->createMenuICVD();
            }
        } elseif ($this->request->REQUEST['is_zhongyou'] || $this->request->REQUEST['zhongyou']) {
            $obj = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
            if ($this->request->REQUEST['is_qingdao']) {
                $result = $obj->createConditionalMenuZhongyou();
            } else {
                $result = $obj->createMenuZhongyou();
            }
        } else {
            $obj = new WXUtil();
            $result = $obj->createMenu();
        }
        $this->setView(0, '', $result);
    }
}
