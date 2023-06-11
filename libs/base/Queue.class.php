<?php
namespace Air\Libs\Base;

class Queue extends \Air\Libs\Redis\Redis {

	protected static $prefix = 'Queue';
    static $xsync = TRUE;

	public static function push($name, $data, $sync = FALSE) {
		return self::lPush($name, urlencode(base64_encode(serialize($data))), $sync);
	}

	public static function pop($name, $multi) {

        if ($multi) { 
            $llen = self::llen($name);
            $len = $llen > 1000 ? 1000 : $llen;
            $i = 1;
            $result = array();
            for($i; $i <= $len; $i++) {
                $result[] = unserialize(base64_decode(self::rPop($name)));
            }
            return $result;
        }

		return unserialize(base64_decode(self::rPop($name)));
	}

	protected static function getLength($name) {
		return self::lSize($name);
	}

}
