<?php
namespace Air\Package\Wechat\Helper;
/**
 * Date: 2019/02/27
 */
class DBWechatConfigTemplateHelper extends \Phplib\Db\DBModel
{
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = 'wechat_config_template';

    public static $fields = array(
        'id',
        'wechat_config_id',
        'template_name',
        'template_id',
        'template_type',
        'template_content',
    );
}
