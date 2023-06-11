<?php
namespace Air\MQ\Sync_to_algo;

use Air\Package\Ai\AIParams;
use Air\Package\Checklist\CheckDiseaseMap;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckLog;
use Phplib\Tools\Logger;

class MQSyncToAlgoProducer extends \Phplib\Kafka\MQProducer {
    const _CLUSTER_ = 'cluster';
    const _TOPIC_ = 'SYNC_TO_ALGO';
    
    public static $confs = array();

    public static function syncCheckDiseaseDetails($check_id, $levels, $flush = true)
    {
        Logger::info(['check_disease_details', $check_id, $levels, $flush], 'sync_to_algo', ['check_id' => $check_id]);
        $event = 'check_disease_details';
        $cdms = CheckDiseaseMap::getAllMapsByCheckIds($check_id, true);
        $tmp_data = [];
        if ($cdms) {
            foreach ($cdms as $cdm_item) {
                // 21~40、80的需要同步
                // 1～20、90的未删除(status>0)的需要同步
                if (empty($levels) && ($cdm_item['status'] || in_array($cdm_item['level'], [80]) || $cdm_item['level'] > 20 && $cdm_item['level'] <= 40)) {
                    $tmp_data[$cdm_item['level']][$cdm_item['user_id']][$cdm_item['position']][] = strval($cdm_item['did']);
                } elseif (in_array(21, $levels) && $cdm_item['level'] > 20 && $cdm_item['level'] <= 40) {
                    $tmp_data[$cdm_item['level']][$cdm_item['user_id']][$cdm_item['position']][] = strval($cdm_item['did']);
                } elseif (in_array($cdm_item['level'], $levels) && ($cdm_item['status'] || in_array($cdm_item['level'], [80]))) {
                    $tmp_data[$cdm_item['level']][$cdm_item['user_id']][$cdm_item['position']][] = strval($cdm_item['did']);
                }
                $org_id = $cdm_item['org_id'];
            }
        }
        if (!$tmp_data) {
            return false;
        }
        $cobj = new CheckInfo();
        $cobj->setAdmin(1);
        $check_info = $cobj->getCheckInfoByIds($check_id);
        $check_info = $check_info[0];
        list($camera, $product) = AIParams::getCameraProduct($check_info, 'sync_dids');
        $data = [];
        foreach($tmp_data as $level => $user_items) {
            foreach ($user_items as $user_id => $position_items) {
                foreach ($position_items as $position => $dids) {
                    $row = [
                        'check_id' => intval($check_id),
                        'position' => intval($position),
                        'level' => intval($level),
                        'dids' => $dids,
                        'user_id' => intval($user_id),
                        'org_id' => intval($org_id),
                        'check_time' => $check_info['created'],
                        'device_info' => $camera,
                        'product' => $product,
                    ];
                    $data[] = $row;
                }
            }
        }
        $ret = self::produce(['event' => $event, 'data' => $data], $check_id, $flush);
        Logger::info(['check_disease_details', $check_id, $data, $ret], 'sync_to_algo', ['check_id' => $check_id]);
        return $ret;
    }

}
