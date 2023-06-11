<?php

namespace Air\Package\Wechat\Helper;

class DBWxThirdHelper extends \Phplib\Db\DBModel
{
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = 'wx_third';

    public static $fields = array(
        'id',
        'name',
        'appid',
        'access_token',
        'refresh_token',
        'expire_time',
        'status',
        'wx_id',
        'template_id',
        'template_report',
        'created',
        'updated',
    );
}
