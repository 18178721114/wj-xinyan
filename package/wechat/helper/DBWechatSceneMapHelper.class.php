<?php
namespace Air\Package\Wechat\Helper;

class DBWechatSceneMapHelper extends \Phplib\Db\DBModel {
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = 'wechat_scene_map';

    public static $fields = array(
        'id',
        'scene',
        'params',
        'qrcode_url',
        'created',
        'updated',
    );
}
