<?php

namespace Air\Config\Config;

class MySQL extends \Air\Libs\Base\Config
{

    protected function __construct()
    {
        $this->ophthalmology = $this->_ophthalmology();
        $this->feedback = $this->_feedback();
        $this->eye = $this->_eye();
        $this->optometry = $this->_optometry();
        $this->fd16 = $this->_fd16();
        $this->dataplatform = $this->_dataplatform();
    }

    private function _fd16()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze0n8f50f8lobix5po.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'fd16a', 'PASS' => 'FD16a@0306', 'DB' => 'fd16a');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2ze0n8f50f8lobix5po.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'fd16a', 'PASS' => 'FD16a@0306', 'DB' => 'fd16a');
        $config['SLAVES'][1]    = array('HOST' => 'rm-2ze0n8f50f8lobix5po.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'fd16a', 'PASS' => 'FD16a@0306', 'DB' => 'fd16a');
        return $config;
    }

    private function _ophthalmology()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze5435349vn2taerno.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'eye_ikang');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2ze5435349vn2taerno.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'eye_ikang');
        $config['SLAVES'][1]    = array('HOST' => 'rr-2ze0rpinvuzrrlsnd.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'eye_ikang');
        return $config;
    }
    private function _feedback()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'test_feedback');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'test_feedback');
        return $config;
    }
    private function _eye()
    {
        $config = array();
        $config['MASTER']    = ['HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'eye_main_test'];
        $config['SLAVES'][0] = ['HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'eye_main_test'];
        $config['SLAVES'][1] = ['HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'eye_main_test'];
        return $config;
    }

    private function _optometry()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'optometry_test');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'optometry_test');
        $config['SLAVES'][1]    = array('HOST' => 'rm-2zedt4j7u183g922nmo.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'optometry_test');
        return $config;
    }

    private function _dataplatform()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2zeaznc5d1c1290h1co.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'data_stats', 'PASS' => 'Data2o22!', 'DB' => 'data_stats_prod');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2zeaznc5d1c1290h1co.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'data_stats', 'PASS' => 'Data2o22!', 'DB' => 'data_stats_prod');
        return $config;
    }
}
