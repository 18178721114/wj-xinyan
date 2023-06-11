<?php
namespace Air\Package\Wechat\Helper;
/**
 * Date: 2019/02/27
 */
class DBWechatConfigHelper extends \Phplib\Db\DBModel
{
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = 'wechat_config';

    public static $fields = array(
        'id',
        'appid',
        'token',
        'prefix',
        'secret',
        'name',
        'produuct_type',
        'type',
        'relation_applet',
        'relation_payment',
    );
}
