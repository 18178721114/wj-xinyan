<?php
namespace Air\Package\Wechat\Helper;


class RedisWechat extends \Phplib\Redis\Redis
{

    public static $prefix = 'wechat_';
    const AUTO_EXPIRE_TIME = 3600;
    const AUTO_KEY = 'antispam_list_';
    /**
     * @param $key
     * @param $value
     * @param int $expeire_time
     */
    public static function setCache($key, $value, $expeire_time=3600) {
        self::setex($key, $expeire_time, $value);
    }

    /**
     * @param $key
     * @return string
     */
    public static function getCache($key) {
        $cache = self::get($key);
        if ($cache) {
            return $cache;
        }
        return null;
    }
    public static function getTTL($key) {
        return self::ttl($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function delCache($key) {
        return self::del($key);
    }

    public static function setAntispam($key) {
        self::setex(self::AUTO_KEY . $key, self::AUTO_EXPIRE_TIME, 1);
    }

    public static function isAntispam($key) {
        return self::get(self::AUTO_KEY . $key);
    }

}
