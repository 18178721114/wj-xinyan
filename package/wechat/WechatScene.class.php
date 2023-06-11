<?php
namespace Air\Package\Wechat;

use Air\Package\Wechat\Helper\DBWechatSceneMapHelper;

class WechatScene {

    public function __construct() {

    }

    static public function getScene($params)
    {
        if (!is_array($params)) {
            return '';
        }
        sort($params);
        return md5(http_build_query($params));
    } 

     /**
     * @param $params array 
     * qrcode_url string
     */
    static public function addItem($params, $qrcode_url = '')
    {
        if (!is_array($params)) {
            return FALSE;
        }
        $scene = self::getScene($params);
        $data = ['scene' => $scene, 'qrcode_url' => $qrcode_url];
        $old = DBWechatSceneMapHelper::getLines(['scene' => $scene]);
        if (empty($old)) {
            $data['params'] = json_encode($params);
            $id = DBWechatSceneMapHelper::create($data);
            return $id;
        }
        $sql = "UPDATE " . DBWechatSceneMapHelper::_TABLE_ . " SET params = :params WHERE id = :id";
        DBWechatSceneMapHelper::updateDataBySql($sql, ['params' => json_encode($params), 'id' => $old[0]['id']]);
        return $old[0]['id'];
    }

    /**
     * @param $scene string md5 string
     */
    static public function getItem($scene)
    {
        $old = DBWechatSceneMapHelper::getLines(['scene' => $scene]);
        if (!$old) {
            return [];
        }
        $old[0]['params'] = json_decode($old[0]['params'], 1);
        return $old[0];
    }

}
