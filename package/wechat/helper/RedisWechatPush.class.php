<?php
namespace Air\Package\Wechat\Helper;

class RedisWechatPush extends \Phplib\Redis\Redis {
    const EXPIRE_TIME = 3600;
    public static $prefix = 'RedisWechatPush';
    private static $zset_key = 'push_zset';

    /**
     * @param $id
     * @param $uid
     * @return
     */
    public static function addCheckId($id) {
        self::zadd(self::$zset_key, microtime(1), $id);
        $n = self::zcard(self::$zset_key);
        self::expire(self::$zset_key, self::EXPIRE_TIME);
        return $n;
    }

    public static function getPushTask() {
        $check_ids = self::ZRANGE(self::$zset_key, 0, -1);
        return $check_ids;
    }

    public static function removeOneTask($id) {
        return self::zrem(self::$zset_key, $id);
    }
    public static function mark($id) {
        return self::setex('xinguan_' . $id, 864000, time());
    }
    public static function exist($id) {
        return self::get('xinguan_' . $id);
    }
    public static function setLastTime($date) {
        return self::setex('xinguan_last_time', 864000, $date);
    }
    public static function getLastTime() {
        return self::get('xinguan_last_time');
    }
    public static function inc() {
        return self::incr('xinguan_stats');
    }
    public static function getTotal() {
        return self::get('xinguan_stats');
    }

}
