<?php
namespace Air\Config\Config;

use Air\Libs\Base\Utilities;
use Air\Package\Kms\KmsHandler;

class MySQL extends \Air\Libs\Base\Config {

    protected function __construct()
    {
        $this->ophthalmology = $this->_ophthalmology();
        $this->feedback = $this->_feedback();
        $this->eye = $this->_eye();
        $this->optometry = $this->_optometry();
        $this->fd16 = $this->_fd16();
        $this->cv_cms = $this->_cv_cms();
        $this->dataplatform = $this->_dataplatform();
    }
    static protected function config($func) {
        $func_config = [
            'db_decrypt_func' => function($db, $slave = 0) {
                $result = KmsHandler::getSecretValue(constant('RDS_' . strtoupper($db) . '_' .intval($slave)));
                if (!empty($result['AccountName']) && !empty($result['AccountPassword'])) {
                    return [
                        $result['AccountName'],
                        $result['AccountPassword'],
                    ];
                }
                Utilities::DDMonitor("P3-" . ENV . "-数据库配置错误 $db slave: $slave ", 'cloudm');
                return [];
            }
        ];
        return $func_config[$func] ?? ($func_config[$func] ?? NULL);
    }

    private function _fd16() {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'fd16a', 'PASS' => 'FD16a@0306', 'DB' => 'fd16a');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'fd16a', 'PASS' => 'FD16a@0306', 'DB' => 'fd16a');
        $config['SLAVES'][1]    = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'fd16a', 'PASS' => 'FD16a@0306', 'DB' => 'fd16a');
        return $config;
    }

    private function _ophthalmology() {
        $config = array();
        $config['MASTER']       = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'pre_pangu_w', 'PASS' => 'Pr3-Pangu-W!', 'DB' => 'pre_pangu');
        $config['SLAVES'][0]    = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'pre_pangu_r', 'PASS' => 'Pr3-Pangu-R!', 'DB' => 'pre_pangu');
        return $config;
    }

    private function _feedback() {
        $config = array();
        $config['MASTER']       = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'test_feedback');
        $config['SLAVES'][0]    = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'test_feedback');
        return $config;
    }

    private function _eye() {
       $config = array();
       $config['MASTER']       = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'eye_main_test');
       $config['SLAVES'][0]    = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'eye_main_test');
       $config['SLAVES'][1]    = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'eye_main_test');
       return $config;
    }


    private function _optometry() {
        $config = array();
        $config['MASTER']       = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_w', 'PASS' => 'Airdoc_ask', 'DB' => 'optometry_test');
        $config['SLAVES'][0]    = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'optometry_test');
        $config['SLAVES'][1]    = array('HOST' => 'stagingrds.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'ask_r', 'PASS' => 'Airdoc_ask_r', 'DB' => 'optometry_test');
        return $config;
    }

    private function _cv_cms() {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'cv_cms_w', 'PASS' => 'Aird0cCm$', 'DB' => 'cv_cms');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'cv_cms_r', 'PASS' => 'Aird0cCm$R', 'DB' => 'cv_cms');
        return $config;
    }

    private function _dataplatform() {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'data_stats', 'PASS' => 'Data2o22!', 'DB' => 'data_stats_prod');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'data_stats', 'PASS' => 'Data2o22!', 'DB' => 'data_stats_prod');
        return $config;
    }
}
