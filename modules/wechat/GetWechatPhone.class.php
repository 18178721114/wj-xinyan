<?php

namespace Air\Modules\Wechat;

use \Air\Package\Wechat\WechatMiniProgram;

class GetWechatPhone extends \Air\Libs\Controller
{
    public function run()
    {
        $request = $this->request->REQUEST;
        $code = trim($request['code']);
        $encrypted_data = $request["encryptedData"];
        $iv = $request["iv"];
        $auth = WechatMiniProgram::getAuth($code);
        if (isset($auth['errcode'])) {
            $this->setView(40000, '', $auth['errmsg']);
            return false;
        } else {
            $openid = $auth['openid'];
            $session_key = $auth['session_key'];
            $encrypted_data = str_replace(" ", "+", urldecode($encrypted_data));
            $iv = str_replace(" ", "+", urldecode($iv));
            $data = "";
            $errcode = WechatMiniProgram::decryptData(REGISTER_WX_APPID, $session_key, $encrypted_data, $iv, $data);
            if ($errcode == WechatMiniProgram::OK) {
                $this->setView(0, '', $data);
                return true;
            } else {
                $this->setView($errcode, '', '');
                return false;
            }
            return true;
        }
    }
}
