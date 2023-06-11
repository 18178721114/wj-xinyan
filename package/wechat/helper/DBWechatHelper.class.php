<?php
namespace Air\Package\Wechat\Helper;
/**
 * Date: 2019/02/27
 */
class DBWechatHelper extends \Phplib\Db\DBModel
{
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = 'wechat';

    public static $fields = array(
        'id',
        'openid',
        'phone_crypt',
        'wechat_type',
        'created',
        'updated',
        'nickname',
        'sex',
        'province',
        'city',
        'country',
        'headimgurl',
        'channel_num'
    );
}
