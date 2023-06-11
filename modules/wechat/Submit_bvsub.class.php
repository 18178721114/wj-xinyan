<?php
namespace Air\Modules\Wechat;

use \Air\Libs\Xcrypt;
use \Air\Package\Wechat\Helper\RedisPcodeImgUrl;
use Air\Package\User\PatientCode;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Patient\Patient;

class Submit_bvsub extends \Air\Libs\Controller {
    public function run() {
        if (!$this->_init()) {
            return FALSE;
        }
        $request = $this->request->REQUEST;
        //$check_id = (int) Xcrypt::decrypt(rawurldecode(str_replace(' ', '+', $request['check_id'])));
        $uuid = $this->request->REQUEST['pcode'];
        $old_code = PatientCode::getItemByPcode($uuid);
        $is_new_wechat = $old_code['new_wechat'];


        $data['extra_json']['smoke_history'] = $request['smoke_history'];
        $data['extra_json']['sport_history'] = $request['sport_history'];
        $data['extra_json']['brain_history'] = $request['brain_history'];
        $data['extra_json']['ssy'] = $request['ssy'];
        $data['extra_json']['szy'] = $request['szy'];
        $pobj = new Patient();
        $patient = $pobj->getPatientByUuid($uuid);
        $data['patient_id'] = $patient['patient_id'];
        $result = $pobj->updatePatient($data);
       
        $url = RedisPcodeImgUrl::getCache($uuid);
        $name = RedisPcodeImgUrl::getCache($uuid . '_name');
        $openid = RedisPcodeImgUrl::getCache($uuid . '_openid');
        $url = RedisPcodeImgUrl::getCache($uuid);
        if ($url) {
            \Air\Package\Wechat\WechatUserCheck::sendImageByOpenId($name, $openid, $url, $uuid, $is_new_wechat);
        }
        if (0 && !$result) {
            $this->setView(1, '请勿重复提交', []);        
        } 
        else {
            $this->setView(0, 'success', []);        
        }
        return TRUE;
    }

    private function _init(){
        $request = $this->request->REQUEST;
        if (!isset($request['pcode']) || !$request['pcode']) {
            $this->setView(1, '用户ID不能为空', []);
            return FALSE;
        }
        return TRUE;
    }
}
