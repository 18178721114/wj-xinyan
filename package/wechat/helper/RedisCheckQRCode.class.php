<?php
namespace Air\Package\Wechat\Helper;

class RedisCheckQRCode extends \Phplib\Redis\Redis
{
    const EXPIRE_TIME = 86400;
    public static $prefix = 'RedisCheckQRCodeAK:3';

    /**
     * cache the serialized check info
     * @param $id  string id
     * @param $info string info object
     */
    public static function setCache($id, $info) {
        self::setex($id, self::EXPIRE_TIME, $info);
    }

    /**
     * @param string $id appid+check_id info id
     * @return string unserialized check info object
     */
    public static function getCache($id) {
        $cache = self::get($id);
        if ($cache) {
            return $cache;
        }
        return '';
    }

    /**
     * delete the check info from cache
     * @param $id  check info id
     */
    public static function delCache($id) {
        return self::del($id);
    }


    public static function getTTL($id) {
        return self::ttl($id);
    }
}
