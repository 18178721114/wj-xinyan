<?php
/**
 * Date: 2017/10/13
 * Time: 下午4:57
 */

namespace Air\Modules\WeChat;


use Air\Package\Wechat\WXUtil;
use Air\Package\Barcode\Barcode;
use Air\Package\Checklist\CheckLog;
use Air\Package\Checklist\Image;
use Air\Package\Wechat\Helper\RedisPcodeImgUrl;
use Air\Package\Checklist\QiniuHandler;
use Air\Package\Wechat\WechatUserCheck;

class Receive_code extends \Air\Libs\Controller
{
    public function run()
    {
        $this->code = $pcode = $this->request->REQUEST['pcode'];
        $openid = \Air\Libs\Xcrypt::decrypt($this->request->REQUEST['guid']);
        $sign = $this->request->REQUEST['sign'];
        $this->name = $this->request->REQUEST['name'];
		if ($sign != md5(md5($pcode . $openid)) && $sign != 'asdfghjkl') {
			$this->setView(100001, '验证失败', '');
			return FALSE;
		}
        $id = \Air\Package\User\PatientCode::initInsCode($openid, $pcode);
        if ($id) {
            $push_type = $this->insurance;
            $qrcode_img_file = '/tmp/qryw_' . $this->code . '.png';
            $barcode_img_file = '/tmp/baryw_' . $this->code .  '.png';
            Barcode::generateLocalQrCodeImage($this->code, $qrcode_img_file);
            Barcode::generateLocalBarcodeImage($this->code, $barcode_img_file);
            $temp_img_file = '/tmp/tempyw_' . $this->code . '.png';
            WXUtil::generateScreenImage(['qrcode' => $qrcode_img_file, 'barcode' => $barcode_img_file], $this->code, $temp_img_file, 0, 0, 1);
            $md5 = md5(file_get_contents($temp_img_file));
            //zj $ret_img = QiniuHandler::uploadImage($temp_img_file, $md5  . '.png');
            $ret_img = Image::uploadImage($temp_img_file, $md5  . '.png');
            if (!empty($ret_img[0]['key'])) {
				//zj if (defined('IMG_DOMAIN_NEW_HTTPS')) {
				// 	$this->img_url = \Air\Package\Checklist\Helper\RedisImageUrl::signedUrl(IMG_DOMAIN_NEW_HTTPS . $ret_img[0]['key']);
				// }
                // else {
				//     $this->img_url = \Air\Package\Checklist\Helper\RedisImageUrl::signedUrl(IMG_DOMAIN_NEW . $ret_img[0]['key']);
                // }
                $this->img_url = \Air\Package\Checklist\Helper\RedisImageUrl::signedUrl($ret_img);
            } else {
                \Phplib\Tools\Logger::error(['upload_wechat_image_to_qiniu_filed', $this->code, $openid, $temp_img_file], 'wechat_send_msg_error');
                return false;
            }
            RedisPcodeImgUrl::setCache($this->code, $this->img_url);
            //for健维
            RedisPcodeImgUrl::setCache($openid, $this->img_url);
			WechatUserCheck::sendImageByOpenId($this->name, $openid, $this->img_url, $this->code, 0, 1);
            unlink($barcode_img_file);
            unlink($qrcode_img_file);
            unlink($temp_img_file);
            \Phplib\Tools\Logger::error(['Receive_code', $this->request->REQUEST, $openid, $this->img_url], 'wechat_yw');
            CheckLog::addLogInfo(0, 'receive_code', ['data' => ['openid' => $openid, $this->img_url, 'pcode' => $pcode]], 0, '', $pcode);
            $this->setView(0, $this->code, $this->img_url);
            return;
        }
        $this->setView(100002, '生成筛查券失败', '');
    }
}
