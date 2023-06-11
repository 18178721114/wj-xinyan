<?php

namespace Air\Package\Wechat;

use \Phplib\Tools\Logger;
use \Air\Libs\Base\Utilities;
use \Air\Libs\Xcrypt;
use \Air\Package\Wechat\Helper\DBWechatConfigTemplateHelper;
use \Air\Package\Wechat\Helper\DBWechatConfigHelper;
use \Air\Package\User\Organizer;

class WechatThird
{
    // 获取微信 的配置
    public static function getWechatConfig($data)
    {
        $wechat_config = self::getPublicWechatConfig($data, 1)[0];
        if ($wechat_config['relation_applet']) {
            $relation_applet['id'] =  $wechat_config['relation_applet'];
            $relation_applet['type'] =  2; //类型 1 公众号 2 小程序 3 微信支付
            $wechat_config['applet'] = self::getWechatConfig($relation_applet);
        }
        if ($wechat_config['relation_payment']) {
            $relation_payment['id'] =  $wechat_config['relation_payment'];
            $relation_payment['type'] =  3; //类型 1 公众号 2 小程序 3 微信支付
            $wechat_config['payment'] = self::getWechatConfig($relation_payment);
        }
        if ($wechat_config) {
            $wechat_config_template['wechat_config_id'] =  $wechat_config['id'];
            $wechat_config['template'] = self::getWechatConfigTemplateConfig($wechat_config_template);
        }

        if (!$wechat_config) {
            return  [];
        }
        return $wechat_config;
    }
    //获取基本配置
    public static function getPublicWechatConfig($data)
    {
        if (!empty($data)) {
            $where = '';
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    $where .= " and " . $k . "= :" . $k;
                }
            }
        }
        $sql = "SELECT * FROM " . DBWechatConfigHelper::_TABLE_ . " WHERE 1=1 " . $where;
        $result = DBWechatConfigHelper::getDataBySql($sql, $data);
        if (!$result) {
            return  [];
        }
        return $result;
    }

    //获取模版配置  
    public static function getWechatConfigTemplateConfig($data)
    {
        if (!empty($data)) {
            $where = '';
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    $where .= " and " . $k . "= :" . $k;
                }
            }
        }
        $sql = "SELECT * FROM " . DBWechatConfigTemplateHelper::_TABLE_ . " WHERE 1=1 " . $where;
        $result = DBWechatConfigTemplateHelper::getDataBySql($sql, $data, false, 'template_type');
        if (!$result) {
            return  [];
        }
        foreach ($result as $k => &$v) {
            $v['template_content'] = json_decode($v['template_content'], 1);
        }

        return $result;
    }
    /**
     * 获取微信配置，用于机构配置
     */
    public static function getWetchatConfToOrg()
    {
        $wechat_config_data['type'] =  1;
        $wechat_config = self::getPublicWechatConfig($wechat_config_data);
        $info[0]['id'] = '0';
        $info[0]['name'] = '否';
        foreach ($wechat_config as $key => $value) {
            $wechat['id'] = $value['id'];
            $wechat['name'] = $value['name'];
            $info[] = $wechat;
        }
        return $info;
    }
}
