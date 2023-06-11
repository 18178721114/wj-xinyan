<?php

namespace Air\Config\Config;

use Air\Libs\Base\Utilities;
use Air\Package\Kms\KmsHandler;

class MySQL extends \Air\Libs\Base\Config
{

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

    static protected function config($func)
    {
        $func_config = [
            'db_decrypt_func' => function ($db, $slave = 0) {
                $result = KmsHandler::getSecretValue(constant('RDS_' . strtoupper($db) . '_' . intval($slave)));
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
    private function _fd16()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'fd16a');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'fd16a');
        return $config;
    }

    private function _ophthalmology()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'eye_ikang_production');
        // $config['SLAVES'][0]    = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'eye_ikang_production');
        $config['SLAVES'][0]    = array('HOST' => 'rr-2ze3i1p5z3q257ue7.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'eye_ikang_production');
        $config['SLAVES'][1]    = array('HOST' => 'rr-2ze3i1p5z3q257ue7.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'eye_ikang_production');
        return $config;
    }

    private function _feedback()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'user_feedback_w', 'PASS' => 'uFd_2019_w', 'DB' => 'user_feedback');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'user_feedback_r', 'PASS' => 'ufD_2019_r', 'DB' => 'user_feedback');
        $config['SLAVES'][1]    = array('HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'user_feedback_r', 'PASS' => 'ufD_2019_r', 'DB' => 'user_feedback');
        return $config;
    }
    private function _eye()
    {
        $config = array();
        $config['MASTER']   = ['HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'eye_production'];
        $config['SLAVES'][0] = ['HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'eye_production'];
        $config['SLAVES'][1] = ['HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'eye_production'];
        return $config;
    }

    private function _optometry()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'optometry');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'optometry');
        $config['SLAVES'][1]    = array('HOST' => 'rm-2zedt4j7u183g922n.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'db_decrypt_func' => 'db_decrypt_func', 'DB' => 'optometry');
        return $config;
    }
    private function _cv_cms()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'cv_cms_w', 'PASS' => 'Aird0cCm$', 'DB' => 'cv_cms');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2ze0n8f50f8lobix5.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'cv_cms_r', 'PASS' => 'Aird0cCm$R', 'DB' => 'cv_cms');
        return $config;
    }
    private function _dataplatform()
    {
        $config = array();
        $config['MASTER']       = array('HOST' => 'rm-2zevy8mgq1w36r8s7.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'pangu_admin_r', 'PASS' => 'u3wgKQ#nFWXuMNp6', 'DB' => 'data_stats');
        $config['SLAVES'][0]    = array('HOST' => 'rm-2zevy8mgq1w36r8s7.mysql.rds.aliyuncs.com', 'PORT' => '3306', 'USER' => 'pangu_admin_r', 'PASS' => 'u3wgKQ#nFWXuMNp6', 'DB' => 'data_stats');
        return $config;
    }
}
