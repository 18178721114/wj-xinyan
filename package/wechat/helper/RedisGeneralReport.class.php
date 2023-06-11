<?php
namespace Air\Package\Wechat\Helper;


class RedisGeneralReport extends \Phplib\Redis\Redis
{

    public static $prefix = 'RedisGeneralReport';
    const AUTO_EXPIRE_TIME = 864000;
    /**
     * @param $key
     * @param $value
     * @param int $expeire_time
     */
    public static function setCache($key, $value, $expeire_time = 864000) {
        self::setex($key, $expeire_time, $value);
    }

    /**
     * @param $key
     * @return array
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

}
