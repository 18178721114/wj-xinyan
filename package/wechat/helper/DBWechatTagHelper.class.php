<?php

namespace Air\Package\Wechat\Helper;

/**
 * 表不存在
 */
class DBWechatTagHelper extends \Phplib\Db\DBModel
{
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = 'wechat_tag';

    public static $fields = array(
        'id',
        'tag_id',
        'tag_name'
    );
}
