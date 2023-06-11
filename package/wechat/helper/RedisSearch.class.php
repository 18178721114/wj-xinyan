<?php
namespace Air\Package\Wechat\Helper; 

class RedisSearch extends \Phplib\Redis\Redis {

    const EXPIRE_TIME = 86400;
    const THRESHOLD = 5;
    public static $prefix = 'RedisSearch';

    /**
     * alert when there is continous error within 10 minutes
     * @param $key 
     * @return  alert or not
     */
    static public function monitor($key) {
        $ret = self::incr($key); 
        if ($ret == 1) {
            self::expire($key, self::EXPIRE_TIME);
        }
        if ($ret >= self::THRESHOLD) {
            return FALSE;
        }
        return self::THRESHOLD - $ret;
    }
    static public function getNum($key) {
        $ret = self::get($key); 
        return $ret;
    }
}
