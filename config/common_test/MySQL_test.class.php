<?php

namespace Air\Config\Config;

use Air\Libs\Base\Utilities;
use Air\Package\Kms\KmsHandler;

class MySQL extends \Air\Libs\Base\Config
{

    protected function __construct()
    {
        $this->ophthalmology = $this->_ophthalmology();
    }

    private function _ophthalmology()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'root', 'PASS' => '199361Longlong!', 'DB' => 'wj_xinyan');
        $config['SLAVES'][0]    = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'root', 'PASS' => '199361Longlong!', 'DB' => 'wj_xinyan');
        $config['SLAVES'][1]    = array('HOST' => 'rr-2ze0rpinvuzrrlsnd.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'root', 'PASS' => '199361Longlong!', 'DB' => 'wj_xinyan');
        return $config;
    }

}
