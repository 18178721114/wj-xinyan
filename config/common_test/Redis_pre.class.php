<?php
namespace Air\Config\Config;

class Redis extends \Phplib\Config {

	public function __construct() {
		$this->servers = array(
			//array('host' => '127.0.0.1', 'port' => '6379'),
			//array('host' => '127.0.0.1', 'port' => '6379'),
            array('host' => 'pre-pangu.redis.rds.aliyuncs.com', 'port' => '6379', 'password' => 'Airdoc2022!')
		);
		$this->img_servers = array(
            array('host' => 'speed001.redis.rds.aliyuncs.com', 'port' => '6789', 'password' => 'Airdoc1mage', 'db' => 8),
		);

	}
}
