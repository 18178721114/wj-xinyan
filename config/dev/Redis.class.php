<?php
namespace Air\Config\Config;

class Redis extends \Phplib\Config {

	public function __construct() {
		$this->servers = array(
			array('host' => '127.0.0.1', 'port' => '6379'),
			array('host' => '127.0.0.1', 'port' => '6379'),
		);

	}
}
