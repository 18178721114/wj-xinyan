<?php

namespace Air\Modules\Wechat;

use Air\Package\Wechat\WechatThird;

// 获取微信配置列表jira-1421
class Wechat_config_list extends \Air\Libs\Controller
{

    public $must_login = true;
    public function run()
    {
        $info = WechatThird::getWetchatConfToOrg();
        $this->setView('0', '', $info);
    }
}
