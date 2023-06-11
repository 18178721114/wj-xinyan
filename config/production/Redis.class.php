<?php
namespace Air\Config\Config;

class Redis extends \Phplib\Config {

	public function __construct() {
		$this->servers = array(
			array('host' => 'r-2ze0b169b21d9f64.redis.rds.aliyuncs.com', 'port' => '6379', 'password' => 'redis4Airdoc2018'),
			array('host' => 'r-2ze0b169b21d9f64.redis.rds.aliyuncs.com', 'port' => '6379', 'password' => 'redis4Airdoc2018'),
		);
        $this->img_servers = array(
            array('host' => 'speed001.redis.rds.aliyuncs.com', 'port' => '6789', 'password' => 'Airdoc1mage'),
        );

	}
}
