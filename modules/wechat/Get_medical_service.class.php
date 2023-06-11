<?php
namespace Air\Modules\Wechat;

use Air\Package\Thirdparty\ThirdHandler;
use Air\Package\User\PatientCode;
use Air\Package\Wechat\WXUtil;

class Get_medical_service extends \Air\Libs\Controller
{
    public function run()
    {
        $request = $this->request;
        $code = $this->request->REQUEST['code'];
        $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        $result = $wx_util->getAuthAccessToken($code);
        $openid = $result['openid'];
        if ($openid) {
            $check_id =  PatientCode::getLatestValidCheckId($openid);
            if ($check_id) {
                $url = ThirdHandler::pushWeilaibaobeiReport($check_id);
                if ($url) {
                    header("Location: " . $url);
                    exit;
                }
            }
        }
        $not_found_url = EYE_DOMAIN_HTTPS . 'h5-v2/notReport';
        header("Location: " . $not_found_url);
        exit;
    }
}
