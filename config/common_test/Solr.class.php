<?php
namespace Air\Config\Config;

class Solr extends \Phplib\Config {

	public function __construct() {
		$this->servers = array(
			array('host' => '101.201.196.78', 'port' => '8983'),
		);

	}
}
