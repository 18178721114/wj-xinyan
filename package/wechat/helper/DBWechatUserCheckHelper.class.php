<?php
namespace Air\Package\Wechat\Helper;

class DBWechatUserCheckHelper extends \Phplib\Db\DBModel {
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = 'wechat_user_check';

    public static $fields = array(
        'id',
        'open_id',
        'check_id',
        'status',
        'type',
        'new_wechat',
        'created',
        'updated',
        'platform',
    );
}
