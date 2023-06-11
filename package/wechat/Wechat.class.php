<?php

namespace Air\Package\Wechat;

use \Phplib\Tools\Logger;
use \Air\Libs\Base\Utilities;
use \Air\Libs\Xcrypt;
use \Air\Package\Wechat\Helper\DBWechatHelper;
use \Air\Package\User\Organizer;

class Wechat
{
    public function __construct()
    {
    }

    public static function addRecord($data)
    {
        if (!empty($data['phone'])) {
            $data['phone_crypt'] = Xcrypt::aes_encrypt($data['phone']);
            unset($data['phone']);
        }
        return DBWechatHelper::create($data);
    }

    public static function getRecordByOpenid($openid)
    {
        $result = DBWechatHelper::getLines(['openid' => $openid], true);
        if (!empty($result)) {
            $result[0]['phone'] = Xcrypt::aes_decrypt($result[0]['phone_crypt']);
            return $result[0];
        }

        return [];
    }

    public static function getRecordByPhone($phone)
    {
        $phone_crypt = Xcrypt::aes_encrypt($phone);
        $result = DBWechatHelper::getLines(['phone_crypt' => $phone_crypt], true);
        if (!empty($result)) {
            $result[0]['phone'] = Xcrypt::aes_decrypt($result[0]['phone_crypt']);
            return $result[0];
        }
        return [];
    }

    public static function updateByOpenid($openid, $data)
    {
        $data['updated'] = date('Y-m-d H:i:s');
        if (!empty($data)) {
            $update_fields = [];
            if (is_array($data)) {
                foreach ($data as $key => $val) {
                    $update_fields[] = $key . '=:' . $key;
                }
            } else {
                $update_fields[] = $data;
            }

            return DBWechatHelper::updateDataBySql('UPDATE ' . DBWechatHelper::_TABLE_ . ' SET ' . join(',', $update_fields) . ' WHERE openid="' . $openid . '"', $data);
        }

        return FALSE;
    }
}
