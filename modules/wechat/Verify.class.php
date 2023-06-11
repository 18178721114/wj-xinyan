<?php
namespace Air\Modules\Wechat;

use Air\Libs\Xcrypt;
use Air\Package\Distribution\Distribution;
use \Air\Package\User\User;
use \Air\Package\Session\Helper\RedisSession;
use \Air\Package\User\Helper\RedisAntispam;

class Verify extends \Air\Libs\Controller {

    public function run(){
        if (!$this->_init()) {
            return false;
        }
        $mobile = trim($this->request->REQUEST['phone']);
        $frm = trim($this->request->REQUEST['frm']);
        if ($frm == 'distribution') {
            $data_distribution['phone'] = Xcrypt::aes_encrypt($mobile);
            $data_distribution['status'] = 0;
            $data_distribution['check_id'] = 0;
            $data_distribution['subscribe_date'] = date('Y-m-d');
            // $distribution_subscribe = Distribution::getSubscribeByDate($data_distribution);
            // if($distribution_subscribe){
            //     $this->setView(2, '您的手机号已经预约成功，请先进行检测或取消预约', []);
            //     return FALSE;
            // }
        }
        $code = RedisSession::get('wechat' . md5($mobile));
        $ttl = RedisSession::ttl('wechat' . md5($mobile));
        if ($code && $ttl > 60) {
            $this->setView(1, '请不要重复请求！', []);
            return FALSE;
        }
        if (empty($code)) {
            $min = 1000;
            $max = 9999;
            $code = rand($min, $max);
        }
        $data = "{$code} 是您的验证码，2分钟内有效。";
        if (!RedisSession::setex('wechat' . md5($mobile), 120, $code)) {
            //redis设置失败
            $this->setView($this->error_code_prefix . '01', '网络异常，请稍后再试', []);
            return FALSE;
        }
        $ip_key = 'ip-sms-' . $this->request->ip;
        $th = 50;
        $ret_ip = RedisAntispam::inc($ip_key, $th, 180);
        $is_black = RedisAntispam::isBlack($ip_key);
        if ($is_black) {
            $group = ENV == 'production' ? 'cloudm' : 'dev';
            $ret_warning = \Air\Libs\Base\Utilities::DDMonitor("P3-AK-验证码短信太频繁，wechat({$mobile}); 被关小黑屋。IP：{$this->request->ip}。", $group, TRUE);
            $this->setView($this->error_code_prefix . '02', '当前IP存在安全风险，请联系Airdoc运营人员！', []);
            return FALSE;
        }
        if ($frm == 'zx') {
            $data = '【视网膜】' . $data;
        }
        
        $ret = \Phplib\Tools\SmsSDK::send($mobile, $data);
        if (isset($ret['code']) && empty($ret['code'])) {
            $this->setView(0, '发送成功', []);
        }
        else {
            $this->setView(0, '发送失败', []);
        }
    }

    private function _init(){
        //check params
        $request = $this->request;
        $request->REQUEST['phone'] = trim($request->REQUEST['phone']);
        if (!isset($request->REQUEST['phone']) || !$request->REQUEST['phone']) {
            $this->setView($this->error_code_prefix . '02', '手机号码不能为空', []);
            return FALSE;
        }
        if (!\Air\Libs\Base\Utilities::isPhone($request->REQUEST['phone'])) {
            $this->setView($this->error_code_prefix . '03', '手机号码不符合规范', []);
            return FALSE;
        }
        $isBlack = (int) RedisAntispam::isBlack($request->REQUEST['phone']);
        if ($isBlack > 0) {
            $this->setView($this->error_code_prefix . '04', '操作太频繁！请稍后再试', []);
            return FALSE;
        }
        return TRUE;
    }

}

