<?php
/**
 * Created by PhpStorm.
 * User: 翁劲
 * Date: 2018/11/30
 * Time: 10:49
 */

namespace Air\Package\Wechat\Helper;


class RedisPcodeImgUrl extends \Phplib\Redis\Redis
{
    const EXPIRE_TIME = 2592000;
    public static $prefix = 'RedisPcodeImgUrlAK2:';

    /**
     * cache the serialized check info
     * @param $id  check info id
     * @param $info check info object
     */
    public static function setCache($pcode, $img_url) {
        self::setex($pcode, self::EXPIRE_TIME, $img_url);
    }

    /**
     * @param $id check info id
     * @return the unserialized check info object
     */
    public static function getCache($pcode) {
        $img_url = self::get($pcode);
        if ($img_url) {
            return $img_url;
        }
        return [];
    }

    /**
     * delete the check info from cache
     * @param $id  check info id
     */
    public static function delCache($pcode) {
        return self::del($pcode);
    }
}
