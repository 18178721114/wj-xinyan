<?php

namespace Air\Modules\Wechat;

use \Air\Libs\Xcrypt;
use \Air\Package\Fd16\CameraHandler;
use \Air\Package\Wechat\WXUtil;
use \Air\Package\Wechat\helper\RedisPcodeImgUrl;
use \Air\Package\User\PatientCode;

class Pcode_not_used extends \Air\Libs\Controller
{
    private $fd16_start_url = '';

    public function run()
    {
        if (!$this->_init()) {
            return false;
        }
        $pcodes = PatientCode::getItemsByOpenid($this->openid);
        if (!$pcodes || count($pcodes) == 1) {
            $this->setView(10004, gettext('没有未使用的pcode'), '');
            return false;
        }
        foreach ($pcodes as $item) {
            if ($item['pcode'] == $this->pcode) {
                continue;
            }
            if (!$item['check_id'] && $item['patient_id']) {
                $this->fd16_start_url = RedisPcodeImgUrl::getCache($item['pcode'] . '_fd16_url');
            }
            break;
        }
        if (!$this->fd16_start_url) {
            $this->setView(10005, gettext('没有未使用的pcode'), '');
            return false;
        }
        $url_info = parse_url($this->fd16_start_url);
        $query_str = $url_info['query'];
        parse_str($query_str, $querys);
        if (!$querys) {
            $this->setView(10006, gettext('没有未使用的pcode'), '');
            return false;
        }
        if (!$querys['sn'] || $querys['sn'] != $this->sn) {
            $this->setView(10007, gettext('没有未使用的pcode'), '');
            return false;
        }
        $camera = CameraHandler::getCameraBySN($this->sn);
        // $user_id = $camera['user_id'];
        // $user_obj = new User();
        // $this->fd16_user = $user_obj->getUserById($user_id);
        if ($querys['work_mode'] != $camera['work_mode']) {
            $this->setView(10008, gettext('没有未使用的pcode'), '');
            return false;
        }
        if ($querys['ts'] && time() + 300 - $querys['ts'] > CameraHandler::VALID_TIME_IN_SECONDS) {
            $this->setView(10009, gettext('没有未使用的pcode'), '');
            return false;
        }
        $page = WXUtil::h5Url2Miniprogram($this->fd16_start_url);
        if (!strpos('pre' . $page, 'pages')) {
            $this->setView(10010, gettext('没有未使用的pcode'), '');
            return false;
        }
        $this->setView(0, 'success', $page);
        return false;
    }

    private function _init()
    {
        $request = $this->request->REQUEST;
        $en_openid = trim($request['en_openid']);
        $this->openid = '';
        if ($en_openid && strlen($en_openid) > 80) {
            $this->openid = Xcrypt::decrypt($en_openid);
        }
        $this->sn = trim($request['sn']);
        $this->pcode = trim($request['pcode']);
        if (empty($this->openid)) {
            $this->setView(10001, gettext('open为空'), '');
            return false;
        }
        if (empty($this->sn)) {
            $this->setView(10002, gettext('sn为空'), '');
            return false;
        }
        if (empty($this->pcode)) {
            $this->setView(10003, gettext('pcode为空'), '');
            return false;
        }
        return true;
    }

}
