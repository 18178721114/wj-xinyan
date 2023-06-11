<?php

namespace Air\Package\Wechat;

use \Air\Libs\Base\Utilities;
use \Air\Package\Wechat\Helper\DBWechatUserCheckHelper;
use \Phplib\Tools\Logger;
use \Air\Libs\Xcrypt;
use Air\Package\Bisheng\BishengUtil;
use Air\Package\Checklist\CheckInfo;
use \Air\Package\User\PatientCode;
use \Air\Package\Checklist\CheckLog;
use Air\Package\Checklist\CheckPdfMap;
use \Air\Package\Icvd\Helper\DBCheckAgentMapHelper;
use Air\Package\Patient\Patient;
use Air\Package\Pay\CheckOrder;
use Air\Package\Sti\STIHandler;
use Air\Package\Thirdparty\ManniuHandler;
use Air\Package\Thirdparty\ThirdHandler;
use Air\Package\User\Organizer;
use \Air\Package\Wechat\Helper\RedisGeneralReport;
use Air\Package\Wechat\WXUtil;
use Air\Package\User\User;
use Air\Package\Checklist\Helper\RedisImageUrl;
use Air\Package\Distribution\Distribution;
use Air\Package\Fd16\CameraHandler;
use Air\Package\Glg\GlgHandler;
use Air\Package\Icvd\Helper\DBWechatUserHealthAdviceHelper;
use Air\Package\Icvd\WechatPushHealthAdvice;
use Air\Package\Oversea\OverseaReportUtils;
use Air\Package\Recall\PatientRecall;
use Air\Package\Wechat\WechatThird;
use Air\Package\Yt_check_info\RetinaCheckItem;
use Air\Package\Yt_check_info\YtCheckInfo;
use Air\Package\Sme\SMEConfig;

class WechatUserCheck
{
    static public function addItem($data, $repush = 0)
    {
        $old = DBWechatUserCheckHelper::getLines(['open_id' => $data['open_id'], 'check_id' => $data['check_id']], true);
        if (empty($old)) {
            $data['updated'] = date('Y-m-d H:i:s');
            if (!isset($data['new_wechat'])) {
                $data['new_wechat'] = 0;
                if (defined('IS_NEW_WX')) {
                    $data['new_wechat'] = IS_NEW_WX;
                }
            }
            $id = DBWechatUserCheckHelper::create($data);

            $check_log_remark = $data;
            CheckLog::addLogInfo($data['check_id'], 'wechat_user_check_create', $check_log_remark);

            return ['open_id' => $data['open_id'], 'check_id' => $data['check_id'], 'id' => $id];
        }
        $sql = '';
        $p = [];
        if ($repush == 1 && $old[0]['status'] == 4) {
            $sql = "UPDATE " . DBWechatUserCheckHelper::_TABLE_ . " SET status = :status WHERE id = :id";
            $p = ['status' => 3, 'id' => $old[0]['id']];
            $ret = DBWechatUserCheckHelper::updateDataBySql($sql, $p);
        }
        if ($repush == 2) {
            $sql = "UPDATE " . DBWechatUserCheckHelper::_TABLE_ . " SET status = :status WHERE id = :id";
            $p = ['status' => 5, 'id' => $old[0]['id']];
            $ret = DBWechatUserCheckHelper::updateDataBySql($sql, $p);
        }

        $check_log_remark = ['sql' => $sql, 'param' => $p];
        CheckLog::addLogInfo($data['check_id'], 'wechat_user_check_update', $check_log_remark);

        return $old[0];
    }
    static public function handleUpgrade($old_id, $new_id)
    {
        $old = DBWechatUserCheckHelper::getLines(['check_id' => $old_id], true);
        if (empty($old)) {
            Logger::alert("old openid is not defined, old check_id {$old_id}, new check_id {$new_id}!", 'upgrade_error');
            return FALSE;
        }
        $data = ['open_id' => $old[0]['open_id'], 'check_id' => $new_id, 'status' => 0];
        $id = DBWechatUserCheckHelper::create($data);
        return $id;
    }
    static public function getItemById($id)
    {
        $data = DBWechatUserCheckHelper::getLines(['id' => $id], true);
        if (!$data) {
            return [];
        }
        return $data[0];
    }
    static public function getToBePush($arg0)
    {
        if ($arg0) {
            $sql = "select * FROM " . DBWechatUserCheckHelper::_TABLE_ . " WHERE status IN (0,3,5) and updated > :start AND updated < :end LIMIT 3000";
            $data = DBWechatUserCheckHelper::getDataBySql($sql, ['start' => date('Y-m-d H:i:s', time() - 3600 * 72), 'end' => date('Y-m-d H:i:s', time() - 3900)], true);
        } else {
            $sql = "select * FROM " . DBWechatUserCheckHelper::_TABLE_ . " WHERE status IN (0,3,5) and updated > :start LIMIT 1500";
            $data = DBWechatUserCheckHelper::getDataBySql($sql, ['start' => date('Y-m-d H:i:s', time() - 3600)], true);
        }
        if (!$data) {
            return [];
        }
        return array_reverse($data);
    }

    static public function handleDelay24Hours()
    {
        $sql = "select id FROM " . DBWechatUserCheckHelper::_TABLE_ . " WHERE status = 10 and updated > :start AND created < :created LIMIT 3000";
        $data = DBWechatUserCheckHelper::getDataBySql($sql, ['start' => date('Y-m-d H:i:s', time() - 3600 * 24), 'created' => date('Y-m-d H:i:s', time() - 3600 * 23.75)], true);
        if (!$data) {
            Logger::info(['function' => 'handleDelay24Hours', 'affected' => 0, 'ids' => []], 'handle_delay_24hours');
            return 0;
        }
        $ids = [];
        foreach ($data as $item) {
            $ids[] = $item['id'];
        }
        $num = WechatUserCheck::updateStatus($ids, 0);
        Logger::info(['function' => 'handleDelay24Hours', 'affected' => $num, 'ids' => $ids], 'handle_delay_24hours');
        return $num;
    }
    static public function handleDelay($data)
    {
        $sql = "select id FROM " . DBWechatUserCheckHelper::_TABLE_ . " WHERE status = :status and updated > :start AND created < :created LIMIT 3000";
        $data = DBWechatUserCheckHelper::getDataBySql($sql, ['status' => $data['status'], 'start' => $data['start'], 'created' => $data['end']], true);
        if (!$data) {
            Logger::info(['function' => 'handleDelay', 'affected' => 0, 'ids' => []], 'handle_delay');
            return 0;
        }
        $ids = [];
        foreach ($data as $item) {
            $ids[] = $item['id'];
        }
        $num = WechatUserCheck::updateStatus($ids, 0);
        Logger::info(['function' => 'handleDelay', 'affected' => $num, 'ids' => $ids], 'handle_delay');
        return $num;
    }

    static public function getToBePush4XinGuan($last_time)
    {
        $sql = "SELECT * FROM " . DBWechatUserCheckHelper::_TABLE_ . " WHERE created BETWEEN :start AND :end AND check_id > 0 AND open_id != '' ORDER BY created";
        $data = DBWechatUserCheckHelper::getDataBySql($sql, ['end' => date('Y-m-d H:i:s', strtotime($last_time) + 86400), 'start' => $last_time], true);
        if (!$data) {
            return [];
        }
        return $data;
    }
    static public function getWechatByCheckId($check_id)
    {
        $sql = "select * FROM " . DBWechatUserCheckHelper::_TABLE_ . " WHERE check_id = :check_id";
        return DBWechatUserCheckHelper::getDataBySql($sql, ['check_id' => $check_id], true);
    }
    static public function getToBePushWechatByCheckId($check_id)
    {
        return DBWechatUserCheckHelper::getLines(['check_id' => $check_id, 'status' => 0], true);
    }
    static public function updateStatus($id, $status, $check_id = 0)
    {
        if (!$id && !$check_id) {
            return FALSE;
        }
        \Phplib\Tools\Logger::info("id: $id check_id: $check_id status: $status", 'wechat_user_check_status');
        $param = ['status' => $status, 'id' => $id];
        if ($id) {
            $wechat_user_check_list = DBWechatUserCheckHelper::getLines(['id' => $id], true);

            if (is_array($id)) {
                $sql = "UPDATE " . DBWechatUserCheckHelper::_TABLE_ . " SET status = :status WHERE id IN (" . implode(',', $id) . ")";
                unset($param['id']);
            } else {
                $sql = "UPDATE " . DBWechatUserCheckHelper::_TABLE_ . " SET status = :status WHERE id = :id";
            }
        } else {
            $wechat_user_check_list = DBWechatUserCheckHelper::getLines(['check_id' => $check_id], true);

            $sql = "UPDATE " . DBWechatUserCheckHelper::_TABLE_ . " SET status = :status WHERE check_id  = :id";
            $param['id'] = $check_id;
        }
        $ret = DBWechatUserCheckHelper::updateDataBySql($sql, $param);

        $check_log_remark = [];
        $check_log_remark_check_id = [];
        foreach ($wechat_user_check_list as $item) {
            $check_log_remark_check_id[$item['check_id']] = $item['check_id'];
            $check_log_remark[$item['check_id']][] = ['change' => "{$item['status']} - $status"];
        }
        foreach ($check_log_remark_check_id as $item) {
            $check_log_remark_item = ['sql' => $sql, 'param' => $param, 'column' => 'status', $check_log_remark[$item]];
            CheckLog::addLogInfo($item['check_id'], 'wechat_user_check_update', $check_log_remark_item);
        }

        return $ret;
    }
    static public function wechatRead($check_id)
    {
        if (!$check_id) {
            return FALSE;
        }
        $old = DBWechatUserCheckHelper::getLines(['check_id' => $check_id, 'status' => [1, 2]], true);
        if ($old) {
            CheckLog::addLogInfo($check_id, 'wechat_read', ['lang' => "阅读微信消息"]);
            $sql = "UPDATE " . DBWechatUserCheckHelper::_TABLE_ . " SET status = :status WHERE id = :id";
            $ret = DBWechatUserCheckHelper::updateDataBySql($sql, ['status' => 4, 'id' => $old[0]['id']]);
        }
        return '';
    }
    public function getByOpenid($openid, $page = 0)
    {
        $ret = DBWechatUserCheckHelper::getLines(["open_id" => $openid, 'status' => [1, 4]], true, '', ['id' => 1], ['limit' => ['offset' => $page * 50, 'page_num' => 50]]);
        if (!$ret) {
            return [];
        }
        return $ret;
    }
    static public function sendMsgByOpenId4YW($item)
    {
        $openid = $item['openid'];
        $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        $template_id = ICVD_REPORT_NOTICE_TEMPLATE_ID;
        $title = '鹰瞳健康';
        $title_first = $item['name'] . '您好，';
        $title_first = $title_first . '您的鹰瞳健康报告已生成。';
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $item['url'],
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $title,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '详细报告请点击查看',
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data);
        if ($result) {
            \Phplib\Tools\Logger::info(['send_report_msg_success', $item, $result], 'wechat_yw');
        } else {
            \Phplib\Tools\Logger::error(['send_report_msg_failed', $item, $result], 'wechat_yw');
        }
        return $result;
    }
    static public function sendWarningMsg($item, $warning_info, $check_info = [])
    {
        //设置语言处理
        // Utilities::restoreI18n();
        if ($check_info) {
            if (!$check_info['org']) {
                if ($check_info['org_id'] > 0) {
                    $org_info = Organizer::getOrgSubsidiaryByIds($check_info['org_id']);
                    $org_info = $org_info[0];
                    $report_lang = $org_info['config']['report_lang'];
                }
            } else {
                $org_info = $check_info['org'];
                $report_lang = $org_info['config']['report_lang'];
            }
            if ($report_lang) {
                Utilities::setI18n($report_lang);
            }
        }

        $openid = $item['open_id'];
        $sub_openid = substr($openid, 0, 5);
        // 微信公众号可配置 jira- 1421
        $wechat_config_data['prefix'] =  $sub_openid;
        $wechat_config_data['type'] = 1;
        $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        if ($wechat_config) {
            $obj = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
            $template_id = $wechat_config['template'][5]['template_id'];
        } elseif ($sub_openid == YTHEALTH_WX_OPENID_PREFIX) {
            $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            $template_id = YTHEALTH_WX_HEALTH_WARNING;
        } elseif ($item['new_wechat'] == 4) {
            $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
            $template_id = TZJ_WX_HEALTH_WARNING;
        } elseif (in_array($item['new_wechat'], [1])) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
            $template_id = WX_HEALTH_WARNING_NEW;
        } else {
            $obj = new WXUtil();
            $template_id = WX_HEALTH_WARNING;
        }
        $title = gettext('经三甲医院专家复核确认');
        $title_first = $item['name'] . gettext('您好，');
        $title_first = $title_first . gettext('您的视网膜影像提示重大健康风险，建议立即就医。');
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => EYE_DOMAIN_HTTPS_PE . "user/warning/" . urlencode(Xcrypt::encrypt($item['check_id'])),
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $title,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $warning_info['auto_disease'],
                    'color' => '#e74478'
                ],
                'keyword3' => [
                    'value' => getenv('LANG') ? date(Utilities::getLocaleDateFormat(getenv('LANG'), 'ymd')) : date('n月j日 H:i'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => gettext('专家建议：') . $warning_info['expert_advice'],
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data);
        if ($result) {
            \Phplib\Tools\Logger::info(['send_warning_msg_success', $item, $warning_info], 'wechat_severe');
        } else {
            \Phplib\Tools\Logger::error(['send_warning_msg_failed', $item, $warning_info], 'wechat_severe');
        }
        return $result;
    }

    static public function sendGlgMsgByOpenId($item, $checkinfo, $from_qr = 0, $url)
    {
        $openid = $item['open_id'];
        if (strpos($openid, WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil();
            $template_id = WX_REPORT_TEMPLATE_ID;
            $whichPA = 1;
        } elseif (strpos($openid, ICVD_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            $template_id = ICVD_REPORT_NOTICE_TEMPLATE_ID;
        } else {
            Logger::error(['sendGlgMsgByOpenId openid is error', $openid], 'wechat_severe');
            Utilities::DDMonitor('sendGlgMsgByOpenId error openid: ' . $openid . ' check_id: ' . $checkinfo['check_id'], 'cloudm', TRUE);
            return FALSE;
        }
        $name = '眼知健健康评估报告';
        $keyword3 = '请点击查看报告详情';
        $title_first = $checkinfo['patient']['name'] . '您好';
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = '您好';
        }
        $remark = "";
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => date('Y年m月d日', strtotime($checkinfo['created'])),
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $keyword3,
                    'color' => '#ff0000'
                    // 'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data, $checkinfo);
        $data = $item;
        if ($result && $data) {
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            $from_qr && $event = 'wechat_push_success_from_qr';
            CheckLog::addLogInfo($checkinfo['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
            self::updateStatus($data["id"], 1);
        } elseif ($data) {
            \Phplib\Tools\Logger::error([$item, $result], 'wechat_send_msg_error');
            self::updateStatus($data["id"], 2);
        } else {
            \Phplib\Tools\Logger::error([$item, $result], 'wechat_send_msg_error');
        }

        return $result;
    }

    static public function sendMsgByOpenId($item, $checkinfo, $from_qr = 0)
    {
        //设置语言处理
        $report_lang = '';
        // // Utilities::restoreI18n();
        if ($checkinfo) {
            if (!$checkinfo['org']) {
                if ($checkinfo['org_id'] > 0) {
                    $org_info = Organizer::getOrgSubsidiaryByIds($checkinfo['org_id']);
                    $org_info = $org_info[0];
                    $report_lang = $org_info['config']['report_lang'];
                }
            } else {
                $org_info = $checkinfo['org'];
                $report_lang = $org_info['config']['report_lang'];
            }
            if ($report_lang) {
                Utilities::setI18n($report_lang);
                $report_lang = Utilities::getLocale($report_lang);
            }
        }

        //Logger::info(['item' => $item, 'check_info' => $checkinfo], 'send_msg_by_openid');
        $openid = $item['open_id'];
        $sub_openid = substr($openid, 0, 5);
        // 微信公众号可配置 jira- 1421
        $wechat_config_data['prefix'] =  $sub_openid;
        $wechat_config_data['type'] = 1;
        $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        if ($wechat_config) {
            return self::sendThirdMsgByOpenId($item, $checkinfo, $from_qr, $wechat_config);
        }
        if (strpos($openid, YTHEALTH_WX_OPENID_PREFIX) !== FALSE) {
            return self::sendYTHealthMsgByOpenId($item, $checkinfo, $from_qr);
        }
        if (strpos($openid, TZJ_WX_OPENID_PREFIX) !== FALSE) {
            return self::sendTZJMsgByOpenId($item, $checkinfo, $from_qr);
        }
        if (strpos($openid, ZY_WX_OPENID_PREFIX) !== FALSE) {
            return self::sendZYMsgByOpenId($item, $checkinfo, $from_qr);
        }
        if (strpos($openid, ICVD_WX_OPENID_PREFIX) !== FALSE || $item['hxt_plus_agent']) {
            return self::sendICVDMsgByOpenId($item, $checkinfo, $from_qr);
        }
        if ($item['new_wechat'] == 6) { //微信开发第三方wxkf处理
            return self::sendWxkfMsgByOpenId($item, $checkinfo, $from_qr);
        }
        if (strpos($openid, WX_OPENID_PREFIX_NEW) === 0) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
            $template_id = WX_REPORT_TEMPLATE_ID_NEW;
            $whichPA = 2;
        } elseif (strpos($openid, WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil();
            $template_id = WX_REPORT_TEMPLATE_ID;
            $whichPA = 1;
        } else {
            return FALSE;
        }
        $check_id = $checkinfo['check_id'];
        RetinaCheckItem::onGenerateReport($check_id);

        $name = gettext('视网膜检查报告单');
        $keyword3 = $item['type'] ? gettext('请补充个人信息后查看报告') : gettext($checkinfo['ikang_wording_3']['suggestion']);
        $title_first = $checkinfo['patient']['name'] . gettext('您好，');
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = gettext('您好，');
        }
        if ($checkinfo['display_upgrade']) {
            $title_first = $title_first . gettext('您的视网膜检查报告已通过审核。');
        } elseif ($item['status'] == 3) {
            $title_first = $title_first . gettext('您的视网膜检查报告经过专家审核，已经被更新。');
        } elseif ($item['status'] == 5) {
            $title_first = $title_first . gettext('您的报告已经经过专家评估，请查看专家意见。');
        } else {
            if ($checkinfo['package_type'] && $checkinfo['patient_info_status'] == 2 && in_array($checkinfo['review_status'], [20, 40])) {
                $title_first = $title_first . gettext('您的视网膜最终报告已生成。');
            } else {
                $title_first = $title_first . gettext('您的视网膜评估报告已生成。');
            }
        }
        $domain = defined('EYE_DOMAIN_HTTPS_PE') ? EYE_DOMAIN_HTTPS_PE : EYE_DOMAIN;
        $remark = $item['type'] ? '' : $checkinfo['ikang_wording_' . $checkinfo['package_type']]['recheck_time'];
        $url = self::getYingtongUrl($checkinfo);
        $url = self::formatUserReportUrl($url);
        $url .= "open_id=" . $openid . "&whichPA=" . $whichPA;
        //$url = $domain . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id'])) . "?open_id=" . $openid . "&whichPA=" . $whichPA;
        //if (substr($checkinfo['patient']['uuid'], 0, 4) == ZX_PCODE_PREFIX) {
        //$url = $domain . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id'])) . "?open_id=" . $openid . "&whichPA=" . $whichPA;
        //}
        $url = self::getUserReportUrl($checkinfo, $url);
        if ($checkinfo['push_report_type'] == 'pdf') {
            $is_simple = 0;
            if ($checkinfo['ext_json']['sub_type_2'] == 'yt_medical_mv' || $checkinfo['ext_json']['sub_type_2'] == 'yt_medical_icvd') {
                $is_simple = 1;
            }
            $pdf = CheckPdfMap::getPdfByCheckId($checkinfo['check_id'], 0, $is_simple);
            if (!$pdf) {
                return false;
            }
            $pdf['pdf_url_signed'] = RedisImageUrl::signedUrl($pdf['pdf_url'], 8640000);
            $url = $domain . 'h5-v2/showPdf?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id'])) . '&pdf=' . urlencode(urlencode($pdf['pdf_url_signed']));
            if ($url && $checkinfo['org']['display_download'] != 1) {
                $url .= '&type=dcg';
            }
            if ($report_lang) {
                $url .= '&language=' . $report_lang;
            } else {
                $url .= '&language=zh_CN';
            }
        }
        // if ($checkinfo['org_id'] == 40068 && ENV == 'production') {
        //     $check_info['pdf'] = CheckPdfMap::getPdfByCheckId($checkinfo['check_id']);
        //     $url = RedisImageUrl::signedUrl($checkinfo['pdf']['pdf_url']);
        // }
        $org_id = $checkinfo['org_id'];
        if ($org_id == PA_ZY_ORG_ID && in_array(PA_SWITCH, [1, 2])) {
            $check_order = CheckOrder::checkExistByPcode($checkinfo['patient']['uuid'], PA_ZY_ORG_ID);
            if ($check_order) {
                $not_push_config = 1;
            }
        } elseif ($org_id == PA_APP_ORG_ID && in_array(PA_APP_SWITCH, [1, 2])) {
            $check_order = CheckOrder::checkExistByPcode($checkinfo['patient']['uuid'], PA_APP_ORG_ID);
            if ($check_order) {
                $not_push_config = 1;
            }
        }
        $push_config = Organizer::getPushConfig($org_id);
        if ($push_config && !$from_qr && !$not_push_config) {
            $remark = $push_config['template_message_remark'] ? $push_config['template_message_remark'] : $remark;
            $keyword3 = $push_config['template_message_remark'] ? $push_config['template_message_remark'] : $keyword3;
            if ($push_config['template_message_has_link']) {
                $url = $push_config['template_message_link'];
            } else {
                $url = '';
            }
        }

        $date_format = getenv('LANG') ? Utilities::getLocaleDateFormat(getenv('LANG'), 'ymd') : 'Y年m月d日';
        $keyword2 = $checkinfo['start_time'] > '2017-01-01' ?  date($date_format, strtotime($checkinfo['start_time'])) : date($date_format, strtotime($checkinfo['created']));

        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $keyword2,
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $keyword3,
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];
        // BAEQ-1981
        if (strpos($openid, WX_OPENID_PREFIX) === 0) {
            if ($remark && strpos('pre' . $remark, '建议下一次复查时间：')) {
                $remark_gray = str_replace('建议下一次复查时间：', '', $remark);
                $remark_gray = "请在{$remark_gray}复查，祝您健康";
            } else {
                $remark_gray = $keyword3;
            }
            if ($report_lang) { //如果有语言，暂时使用建议文案
                $remark_gray = $keyword3;
            }
            // YEHSO-35
            if ($checkinfo['slitlamp_fd16'] == 1) {
                $url = self::handleSlitlampFd16Url($checkinfo);
            }
            $data = [
                'touser' => $openid,
                'template_id' => $template_id,
                'url' => $url,
                'data' => [
                    'first' => [
                        'value' => '',
                        'color' => '#173177'
                    ],
                    'keyword1' => [
                        'value' => $checkinfo['patient']['name'],
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' => gettext('点这里查看报告'),
                        'color' => '#ff0000'
                    ],
                    'keyword3' => [
                        'value' => $remark_gray,
                        'color' => '#173177'
                    ],
                    // 'remark' => [
                    //     'value' => '',
                    //     'color' => '#ff0000'
                    // ]
                ]
            ];
        }
        if ($checkinfo['org_id'] == FD16_ORG_ID) {
            $data['remark'] = [
                'value' => '',
                'color' => '#173177'
            ];
        }
        $result = $obj->pushMessage($data, $checkinfo);
        if ($result === 0) {
            if ($obj->appId == WX_APPID) {
                $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
                $data['template_id'] = WX_REPORT_TEMPLATE_ID_NEW;
                $whichPA = 2;
            } else {
                $obj = new WXUtil(WX_APPID, WX_SECRET);
                $data['template_id'] = WX_REPORT_TEMPLATE_ID;
                $whichPA = 1;
            }
            $result = $obj->pushMessage($data, $checkinfo);
        }
        if (!empty($item['id'])) {
            $data = $item;
        } else {
            $data = DBWechatUserCheckHelper::getLines(['open_id' => $openid, 'check_id' => $checkinfo['check_id']], true);
        }
        if ($result == 0) {
            self::updateStatus($data["id"], 2);
        }
        if ($checkinfo['patient']['status'] && !$from_qr) {
            // notice agent
            $check_agents = DBCheckAgentMapHelper::getLines(['check_id' => $checkinfo['check_id']], true);
            if (!empty($check_agents)) {
                $url = AGENT_TOOL_DOMAIN . 'api/agent/receiveNotice';
                $params = [
                    'agent_num' => $check_agents[0]['agent_num'],
                    'name' => $checkinfo['patient']['name'],
                    'gender' => $checkinfo['patient']['gender'],
                    'type' => 'report_generate'
                ];
                Utilities::curl($url, $params, ['is_post' => 1, 'need_decode' => 1]);
            }
        }
        if ($result && $data) {
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            $from_qr && $event = 'wechat_push_success_from_qr';

            CheckLog::addLogInfo($checkinfo['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
            self::updateStatus($data["id"], 1);
            if ($checkinfo['org_id'] == STI_ORG_ID) {
                $fd16_sn = $checkinfo['sn'];
                $sti_sn = User::getEquipId($checkinfo['submit_user_id']);
                $patient = Patient::getPatientSelfById($checkinfo['patient_id'], true);
                STIHandler::sendMessage($fd16_sn, $sti_sn, 2, $patient['phone'], $checkinfo['check_id'], $checkinfo);
            }
        } elseif ($data) {
            \Phplib\Tools\Logger::error([$item, $result], 'wechat_send_msg_error', ['check_id' => $checkinfo['check_id']]);
            self::updateStatus($data["id"], 2);
        } else {
            \Phplib\Tools\Logger::error([$item, $result], 'wechat_send_msg_error', ['check_id' => $checkinfo['check_id']]);
        }

        return $result;
    }

    static public function handleSlitlampFd16Url($checkinfo, $current = 0)
    {
        $start = date('Y-m-d H:i:s', strtotime($checkinfo['created']) - 60);
        $end = date('Y-m-d H:i:s', strtotime($checkinfo['created']) + 1200);
        $param = [
            'sn' => $checkinfo['ext_json']['sn'],
            'pcode_string' => " url LIKE '%{$checkinfo['patient']['uuid']}%'",
            'created_string' => " created between '$start' AND '$end'",
            'type_string' => ' type IN (16, 17)',
        ];
        $camera_logs = CameraHandler::getCameraLogGeneral($param, 2);
        if ($current) {
            if (count($camera_logs) >= 2) {
                return true;
            }
            return false;
        }
        if (count($camera_logs) < 2) {
            $start = date('Y-m-d 00:00:00');
            $param_right = [
                'sn' => $checkinfo['ext_json']['sn'],
                'created_string' => " created between '$start' AND '$end'",
                'type_string' => ' type IN (17)',
            ];
            $param_left = [
                'sn' => $checkinfo['ext_json']['sn'],
                'created_string' => " created between '$start' AND '$end'",
                'type_string' => ' type IN (16)',
            ];
            $camera_logs_left = CameraHandler::getCameraLogGeneral($param_left, 1);
            $camera_logs_right = CameraHandler::getCameraLogGeneral($param_right, 1);
            $camera_logs = array_merge($camera_logs_left, $camera_logs_right);
        }
        $vidoe_string = implode(',', array_column($camera_logs, 'url'));
        $url = ENV == 'test' ? 'https://test-optometry.airdoc.com/SlitlampStatic' : 'https://optometry.airdoc.com/SlitlampStatic';
        $url .= '?vdo=' . urlencode($vidoe_string);
        return $url;
    }
    static public function sendSMBRiskResultMsgByOpenId($openid, $check_id, $staff_name)
    {
        if (!defined('WX_REPORT_TEMPLATE_ID')) {
            Logger::alert('template id is not defined', 'push_wechat_msg');
            return FALSE;
        }

        $obj = new WXUtil();
        $template_id = WX_REPORT_TEMPLATE_ID;
        $whichPA = 1;
        $name = '众佑员工复工风险检测报告单';
        $title_first = $staff_name . '您好，您的复工风险检测报告已生成。';
        $domain = defined('EYE_DOMAIN_HTTPS_PE') ? EYE_DOMAIN_HTTPS_PE : EYE_DOMAIN;
        $url = $domain . "self/smbReport/" . urlencode(Xcrypt::encrypt($check_id));
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => '安全复工，人人有责！',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '',
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data);
        return $result;
    }

    static public function makeTag($item)
    {
        $openid = $item['open_id'];
        if (strpos($openid, WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil();
        } elseif (strpos($openid, WX_OPENID_PREFIX_NEW) === 0) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
        } else {
            return FALSE;
        }
        $result = $obj->makeTagWrapper($openid);
        return $result;
    }
    static public function sendMsgByOpenId4XinGuan($item, $checkinfo)
    {
        $openid = $item['open_id'];
        if (!defined('WX_REPORT_TEMPLATE_ID')) {
            Logger::alert('template id is not defined', 'xin_guan');
            return FALSE;
        }
        if (strpos($openid, WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil();
            $template_id = WX_REPORT_TEMPLATE_ID;
            $whichPA = 1;
        } elseif (strpos($openid, WX_OPENID_PREFIX_NEW) === 0) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
            $template_id = WX_REPORT_TEMPLATE_ID_NEW;
            $whichPA = 2;
        } else {
            return FALSE;
        }
        $name = '慧心瞳“病毒性肺炎防护建议”模块更新';
        //$title_first = '新冠肺炎患者大部分是轻症，但是对慢病人群和老年人危害较大。慧心瞳报告给老用户免费升级了报告，通过对您的血管健康状况的分析，评估您一旦感染新冠肺炎，发展成重症的风险。';
        $title_first = '慧心瞳报告免费升级，通过您的年龄和慢病风险，给您病毒性肺炎的防护建议，提醒您注意防护，避免感染。';
        $domain = defined('EYE_DOMAIN_HTTPS_PE') ? EYE_DOMAIN_HTTPS_PE : EYE_DOMAIN;
        $url = self::getYingtongUrl($checkinfo);
        $url .= "open_id=" . $openid . "&isXinGuan=1";
        $url = self::getUserReportUrl($checkinfo, $url);
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => '点击查看防护建议',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '点击查看防护建议>>',
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data, $checkinfo);
        if ($result === 0) {
            $result = $obj->pushMessage($data, $checkinfo);
        }
        if ($result) {
            CheckLog::addLogInfo($checkinfo['check_id'], 'wechat_push_xinguan_success', ['data' => ['param' => $data, 'result' => $result]]);
        } else {
            \Phplib\Tools\Logger::error([$item, $result], 'xin_guan');
        }
        return $result;
    }

    static public function sendRegisterMiniprogram($obj, $template_id, $openid, $url, $product, $miniprogram = '')
    {
        $full_name_map = [
            '鹰瞳扫描' => YINGTONG_FULL_NAME,
            '慧心瞳' => HUIXINTONG_FULL_NAME,
            '鹰瞳医疗' => '鹰瞳医疗健康评估',
            '众佑' => ZHONGYOU_FULL_NAME,
            '体知健' => TZJ_FULL_NAME,
            '鹰瞳健康' => YTHEALTH_FULL_NAME,
        ];
        $product_name = $full_name_map[$product] ? $full_name_map[$product] : $product . '健康评估';
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => '欢迎使用' . $product_name . '，请录入您的信息后开始检测。',
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $product,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => date('Y年m月d日', time()),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $miniprogram ? '点击此处填写信息并开始检测' : '点击此处填写信息',
                    'color' => '#ff0000'
                ]
            ]
        ];
        if ($product == '慧心瞳' || strpos('pre' . $product, '鹰瞳医疗') || strpos('pre' . $product, '鹰瞳健康-MV')) {
            $data = [
                'touser' => $openid,
                'template_id' => $template_id,
                'url' => $url,
                'data' => [
                    'first' => [
                        // 'value' => '欢迎使用' . $product_name . '，请录入您的信息后开始检测。',
                        'value' => '',
                        'color' => '#ff0000'
                    ],
                    'keyword1' => [
                        'value' => (strpos('pre' . $product, '鹰瞳医疗') || strpos('pre' . $product, '-MV')) ? gettext($product) : '鹰瞳Airdoc-' . $product,
                        // 'value' => '点这里开始检查登记',
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        // 'value' => date('Y年m月d日', time()),
                        'value' => gettext('点这里开始检查'),
                        'color' => '#ff0000'
                    ],
                    'remark' => [
                        // 'value' => $miniprogram ? '点击此处填写信息并开始检测' : '点击此处填写信息',
                        'value' => '',
                        'color' => '#ff0000'
                    ]
                ]
            ];
        }
        if ($miniprogram) {
            $data['miniprogram'] = [
                'appid' => $miniprogram,
                'pagepath' => $url,
            ];
            $data['url'] = '';
        }
        $result = $obj->pushMessage($data);
        if (!$result || !$data) {
            \Phplib\Tools\Logger::error([$data, $url, $openid], 'wechat_send_msg_error');
        }
        return $result;
    }

    static public function sendImageByOpenId($patient_name, $openid, $img_url, $code, $is_new_wechat, $is_yw = 0, $fd16 = 0, $miniprogram = 0)
    {
        $title_first = '您的筛查码已成功生成。';
        $sub_openid = substr($openid, 0, 5);
        // 微信公众号可配置 jira- 1421
        $wechat_config_data['prefix'] =  $sub_openid;
        $wechat_config_data['type'] = 1;
        $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        if ($wechat_config) {
            $obj = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
            $template_id = $wechat_config['template'][3]['template_id'];
        } elseif (substr($openid, 0, 5) === YTHEALTH_WX_OPENID_PREFIX) {
            $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            $template_id = YTHEALTH_WX_SUBSCRIBE_ID;
        } elseif (substr($openid, 0, 5) === TZJ_WX_OPENID_PREFIX) {
            $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
            $template_id = TZJ_WX_SUBSCRIBE_ID;
        } elseif (substr($openid, 0, 5) === WX_OPENID_PREFIX) {
            $obj = new WXUtil(WX_APPID, WX_SECRET);
            $template_id = WX_SUBSCRIBE_ID;
            $title_first = '您的检查码已成功生成。';
        } elseif (substr($openid, 0, 5) === WX_OPENID_PREFIX_NEW) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
            $template_id = WX_SUBSCRIBE_ID_NEW;
            $title_first = '您的检查码已成功生成。';
        } elseif (substr($openid, 0, 5) === ICVD_WX_OPENID_PREFIX) {
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            $template_id = ICVD_WX_SUBSCRIBE_ID;
            $is_ytsm = 1;
        } elseif (substr($openid, 0, 5) === ZY_WX_OPENID_PREFIX) {
            $obj = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
            $template_id = ZY_WX_SUBSCRIBE_ID;
            $is_zhongyou = 1;
        }
        $url = EYE_DOMAIN_HTTPS_PE . 'user/showImg/' . urlencode($img_url);
        if ($fd16) {
            $url = $img_url;
        }
        if ($is_yw) {
            $url .= "?is_yw=1&pcode=" . urlencode(Xcrypt::encrypt($code));
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            if (substr($code, 0, 4) == ICVD_PCODE_PREFIX) {
                $template_id = ICVD_WX_SUBSCRIBE_ID;
                $is_ytsm = 1;
            }
        }
        if (!$obj) {
            return false;
        }
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $patient_name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => date('Y年m月d日', time()),
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $code,
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '点击此处开始检测',
                    'color' => '#ff0000'
                ]
            ]
        ];
        if ($miniprogram) {
            $data['miniprogram'] = [
                'appid' => REGISTER_WX_APPID,
                'pagepath' => $url,
            ];
            $data['url'] = '';
        }
        if ($is_ytsm || $is_zhongyou) {
            $data['data']['keyword2']['value'] = $code;
            $data['data']['keyword3']['value'] = date('Y年m月d日', time());
        }

        $result = $obj->pushMessage($data);
        if (!$result || !$data) {
            Logger::error([$data, $img_url, $openid], 'wechat_send_msg_error');
        }
        return $result;
    }

    static public function sendImageAgainByOpenId($check_info, $openid, $img_url, $code, $type = 0, $fd16 = 0)
    {
        if (in_array($check_info['org_id'], [SKB_ORG_ID, SKB_ORG_ID_YT])) {
            return FALSE;
        }

        //设置语言处理
        // Utilities::restoreI18n();
        if ($check_info) {
            if (!$check_info['org']) {
                if ($check_info['org_id'] > 0) {
                    $org_info = Organizer::getOrgSubsidiaryByIds($check_info['org_id']);
                    $org_info = $org_info[0];
                    $report_lang = $org_info['config']['report_lang'];
                }
            } else {
                $org_info = $check_info['org'];
                $report_lang = $org_info['config']['report_lang'];
            }
            if ($report_lang) {
                Utilities::setI18n($report_lang);
            }
        }

        $patient_name = $check_info['patient']['name'];
        $sub_openid = substr($openid, 0, 5);
        // 微信公众号可配置 jira- 1421
        $wechat_config_data['prefix'] =  $sub_openid;
        $wechat_config_data['type'] = 1;
        $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        if ($wechat_config) {
            $obj = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
            $template_id = $wechat_config['template'][6]['template_id'];
        } elseif (strpos($openid, YTHEALTH_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            $template_id = YTHEALTH_WX_SHOT_FAIL_ID;
        } elseif (strpos($openid, TZJ_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
            $template_id = TZJ_WX_SHOT_FAIL_ID;
        } elseif (strpos($openid, WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(WX_APPID, WX_SECRET);
            $template_id = WX_SHOT_FAIL_ID;
        } elseif (strpos($openid, WX_OPENID_PREFIX_NEW) === 0) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
            $template_id = WX_SHOT_FAIL_ID_NEW;
        } elseif (strpos($openid, ICVD_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            $template_id = ICVD_WX_SHOT_FAIL_ID;
        } elseif (strpos($openid, ZY_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
            $template_id = ZY_WX_SHOT_FAIL_ID;
        }
        $title_first = '';
        $remark = gettext('可能由于拍摄时眨眼或姿势不对，导致照片模糊，请认真阅读检测流程了解正确的姿势及注意事项，点击此处重新拍摄');
        if ($type) {
            $remark = gettext('系统检测到没有成功拍摄到视网膜，请认真阅读检测流程了解正确的姿势及注意事项，点击此处重新检测');
        }
        $url = EYE_DOMAIN_HTTPS_PE . 'user/showImg/' . urlencode($img_url);
        $keyword4 = gettext('请点击此处重新拍摄');

        $date_format = getenv('LANG') ? Utilities::getLocaleDateFormat(getenv('LANG'), 'ymd') : 'Y年m月d日';

        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => gettext('设备扫描失败提醒'),
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $patient_name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => date($date_format, strtotime($check_info['patient']['created'])),
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $check_info['patient']['uuid'],
                    'color' => '#173177'
                ],
                'keyword4' => [
                    'value' => $keyword4,
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];

        if ($template_id == ZY_WX_SHOT_FAIL_ID) {
            $keword2_tmp = $data['data']['keyword2']['value'];
            $data['data']['keyword2']['value'] = $data['data']['keyword3']['value'];
            $data['data']['keyword3']['value'] = $keword2_tmp;
        }

        if ($fd16) {
            $page = WXUtil::h5Url2Miniprogram($img_url);
            if (strpos('pre' . $page, 'pages')) {
                $data['miniprogram'] = [
                    'appid' => REGISTER_WX_APPID,
                    'pagepath' => WXUtil::h5Url2Miniprogram($img_url),
                ];
                $data['url'] = '';
            } else {
                $data['url'] = $img_url;
            }
        }
        $result = $obj->pushMessage($data, $check_info);
        if (!$result || !$data) {
            Logger::error([$data, $img_url, $openid], 'wechat_send_msg_error');
        }
        return $result;
    }

    static public function sendFailedMsgByOpenId($check_info, $openid, $url, $fd16 = 0)
    {
        if (in_array($check_info['org_id'], [SKB_ORG_ID, SKB_ORG_ID_YT])) {
            return FALSE;
        }

        //设置语言处理
        // Utilities::restoreI18n();
        if ($check_info) {
            if (!$check_info['org']) {
                if ($check_info['org_id'] > 0) {
                    $org_info = Organizer::getOrgSubsidiaryByIds($check_info['org_id']);
                    $org_info = $org_info[0];
                    $report_lang = $org_info['config']['report_lang'];
                }
            } else {
                $org_info = $check_info['org'];
                $report_lang = $org_info['config']['report_lang'];
            }
            if ($report_lang) {
                Utilities::setI18n($report_lang);
            }
        }

        $patient_name = $check_info['patient']['name'];
        $pcode = $check_info['patient']['uuid'];
        $age = $check_info['patient']['age_snapshot'];
        $sub_openid = substr($openid, 0, 5);
        // 微信公众号可配置 jira- 1421
        $wechat_config_data['prefix'] =  $sub_openid;
        $wechat_config_data['type'] = 1;
        $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        if ($wechat_config) {
            $obj = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
            $template_id = $wechat_config['template'][6]['template_id'];
        } elseif (strpos($openid, YTHEALTH_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            $template_id = YTHEALTH_WX_SHOT_FAIL_ID;
        } elseif (strpos($openid, TZJ_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
            $template_id = TZJ_WX_SHOT_FAIL_ID;
        } elseif (strpos($openid, WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(WX_APPID, WX_SECRET);
            $template_id = WX_SHOT_FAIL_ID;
        } elseif (strpos($openid, ICVD_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            $template_id = ICVD_WX_SHOT_FAIL_ID;
        } else {
            Logger::error([$openid, $check_info['check_id'], $check_info['patient']['uuid'], 'openid_invalid: only support huixintong'], 'wechat_send_msg_error');
            return FALSE;
        }
        $title_first = gettext('由于小瞳孔或佩戴位置等原因，我们未能成功采集到您的视网膜图片，请联系工作人员，重新进行操作。');
        if ($age > 50) {
            $title_first = gettext('由于小瞳孔、设备佩戴位置或白内障等原因，我们未能成功采集到您的视网膜图片，请联系工作人员，重新进行操作。');
        }
        if ($check_info['left_bad_img_reason'] == 4 || $check_info['right_bad_img_reason'] == 4) {
            $title_first = gettext('由于佩戴位置不对，您的视网膜照片扫描失败，请联系工作人员，重新进行操作。');
        }
        $keyword4 = gettext('请点击此处重新拍摄');
        $remark = gettext('感谢您的使用');

        $date_format = getenv('LANG') ? Utilities::getLocaleDateFormat(getenv('LANG'), 'ymd') : 'Y年n月d日';

        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#ff0000'
                ],
                'keyword1' => [
                    'value' => $patient_name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => date($date_format, time()),
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $pcode,
                    'color' => '#173177'
                ],
                'keyword4' => [
                    'value' => $keyword4,
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#173177'
                ]
            ]
        ];
        if ($fd16) {
            $page = WXUtil::h5Url2Miniprogram($url);
            if (strpos('pre' . $page, 'pages')) {
                $data['miniprogram'] = [
                    'appid' => REGISTER_WX_APPID,
                    'pagepath' => WXUtil::h5Url2Miniprogram($url),
                ];
                $data['url'] = '';
            } else {
                $data['url'] = $url;
            }
        }
        $result = $obj->pushMessage($data, $check_info);
        if (!$result || !$data) {
            Logger::error([$data, $url, $openid], 'wechat_send_msg_error');
        }
        return $result;
    }


    static public function sendHealthManagementRemindMsg($openid, $c_user_id)
    {
        $sub_openid = substr($openid, 0, 5);
        // $wechat_config_data['prefix'] =  $sub_openid;
        // $wechat_config_data['type'] = 1;
        // $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        // if ($wechat_config) {
        //     $obj = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
        //     $template_id = $wechat_config['template'][6]['template_id'];
        // } else
        if (strpos($openid, YTHEALTH_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            $template_id = YTHEALTH_WX_HEALTH_MANAGEMENT_REMIND_ID;
        } elseif (strpos($openid, TZJ_WX_OPENID_PREFIX) === 0) {
            $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
            $template_id = TZJ_WX_HEALTH_MANAGEMENT_REMIND_ID;
        } else {
            Logger::error([$openid, $c_user_id, 'openid_invalid'], 'wechat_send_msg_error');
            return FALSE;
        }
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            //'url' => EYE_DOMAIN . "user/upgrade/" . urlencode(Xcrypt::encrypt($check_id)),
            'data' => [
                'first' => [
                    'value' => '',
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => date("Y-m-d"),
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => '健康管理评估',
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => '请尽快进行评估',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '持续有规律的检测有助于观察您的健康状况，为您做到早发现，早干预，早治疗',
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data);
        if (!$result) {
            \Phplib\Tools\Logger::error('send_health_management_remind_failed', 'wechat_send_msg_error');
            \Phplib\Tools\Logger::error($data, 'wechat_send_msg_error');
        }
        return $result;
    }

    static public function sendBuySuccessMsg($patient, $check_id)
    {
        $history = DBWechatUserCheckHelper::getLines(['check_id' => $check_id], FALSE, '', ['id' => 1]);
        if (!$history) {
            Logger::alert('sendBuySuccessMsg no_openid error!', 'wechat_send_msg_error');
            return FALSE;
        }
        $openid = $history[0]['open_id'];
        if (!defined('WX_BUY_SUCCESS_TEMPLATE_ID')) {
            Logger::alert('template id is not defined while send buy success message!', 'wechat_send_msg_error');
            return FALSE;
        }
        $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
        $template_id = WX_BUY_SUCCESS_TEMPLATE_ID_NEW;
        $name = '综合套餐';
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => EYE_DOMAIN . "user/upgrade/" . urlencode(Xcrypt::encrypt($check_id)),
            'data' => [
                'first' => [
                    'value' => '您已经成功购买了综合套餐。',
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => '0元',
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => $patient['name'],
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '请点击查看检查单，并向医生出示。',
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data);
        if (!$result) {
            \Phplib\Tools\Logger::error('send_buy_success_failed', 'wechat_send_msg_error');
            \Phplib\Tools\Logger::error($data, 'wechat_send_msg_error');
        }
        return $result;
    }

    static public function handleWechat($check_info, $repush = 0)
    {
        $item = \Air\Package\User\PatientCode::getItemByPcode($check_info['patient']['uuid']);
        $user = User::getUserByIds($check_info['submit_user_id'])[$check_info['submit_user_id']];
        $orgs = Organizer::getOrgSubsidiaryByIds($check_info['org_id']);
        $check_info['push_type'] = ($user['push_type'] == -1) ? $orgs[$check_info['org_id']]['push_type'] : $user['push_type'];
        if ($check_info['ext_json']['push_type']) {
            $check_info['push_type'] = $check_info['ext_json']['push_type'];
        }
        $sms_push_type = $orgs[$check_info['org_id']]['config']['sms_push_type'] ?? 0;
        $check_info_small = [
            'check_id' => $check_info['check_id'],
            'org_id' => $check_info['org_id'],
            'review_status' => $check_info['review_status'],
            'uuid' => $check_info['patient']['uuid'],
            'is_yingtong' => $check_info['is_yingtong'],
            'is_zhongyou' => $check_info['is_zhongyou'],
            'image_quality' => $check_info['image_quality'],
            'patient_status' => $check_info['patient']['status'],
            'push_type' => $check_info['push_type'],
        ];

        \Phplib\Tools\Logger::error([$check_info_small, $item], 'handle_wechat_info', ['check_id' => $check_info['check_id']]);
        if (intval($item['new_wechat']) < 0 && $sms_push_type) {
            $patients = \Air\Package\Patient\Patient::getPatientsByIds($check_info['patient_id'], 'patient_id', 1);
            $phone = \Air\Libs\Xcrypt::aes_decrypt($patients[$check_info['patient_id']]['phone_crypt']);
            $content = '您的Airdoc视网膜慢病评估报告已经生成，请进入微信公众号查看报告。';
            if (substr($check_info['patient']['uuid'], 0, 4) == ZX_PCODE_PREFIX) {
                $content = '【视网膜】您的视网膜健康评估报告已经生成，请进入微信公众号查看报告。';
            }
            if (
                $check_info['review_status'] == 1 && \Air\Libs\Base\Utilities::isPhone($phone)
                && !($check_info['org_id'] == PA_ZY_ORG_ID && PA_SWITCH == 3)
                && !($check_info['org_id'] == PA_APP_ORG_ID && PA_APP_SWITCH == 3)
                && !in_array($check_info['org_id'], [ManniuHandler::MANNIU_ORG_ID[ENV]])
            ) {
                $token = RedisGeneralReport::getCache($phone);
                if ($token) {
                    RedisGeneralReport::setCache($token, $check_info['check_id']);
                }
                // YTMED-97 推送报告类型为不推送报告时，短信也不推送
                if ($check_info['push_type'] != 1) {
                    \Air\Package\User\Sms::smsRecord(['content' => $content, 'phone' => $phone]);
                }
            }
            return FALSE;
        }
        // 不是（双眼不可读+众佑或者鹰瞳）
        if (!($check_info['image_quality'] == 3 && ($check_info['is_yingtong'] || $check_info['is_zhongyou']))) {
            if (empty($item) || empty($item['openid'])) {
                return FALSE;
            }
        }
        //处理30分钟逻辑,判断前面一个单子是否在30分钟以内或点击未推送，不要推送
        // 如果延迟30分钟的类型，且30分钟内没有重新扫描，推送双眼不可读报告
        // 如果延迟30分钟的类型，30分内有重新扫描，不推送双眼不可读报告，只推送最新的报告
        // 如果点击的类型，点击的时候没有重新扫描，推送双眼不可读报告
        // 如果点击的类型，点击的时候有重新扫描，不推送双眼不可读报告，只推送最新的报告

        //30分钟和点击推送场景增加处理去重
        $last_check_infos = CheckInfo::getLatestCheckInfoByPatientId($check_info['patient_id'], 2);
        if (!empty($last_check_infos) && count($last_check_infos) > 1) {
            $last_check_info = $last_check_infos[1];
            $data_info = WechatUserCheck::getWechatByCheckId($last_check_info['check_id']);
            if (!empty($data_info)) {
                if ($data_info['status'] == 0) {
                    WechatUserCheck::updateStatus($data_info['id'], 1);
                }
            }
        }
        $type = 1;
        if ($check_info['patient']['status'] >= 1) {
            $type = 0;
        }
        $openid = $item['openid'];
        $data = ['open_id' => $item['openid'], 'check_id' => $check_info['check_id'], 'type' => $type, 'new_wechat' => $item['new_wechat']];
        $sub_openid = substr($openid, 0, 5);
        // 微信公众号可配置 jira- 1421
        $wechat_config_data['prefix'] =  $sub_openid;
        $wechat_config_data['type'] = 1;
        $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
        if ($item['new_wechat'] != 6 && !$wechat_config && strpos($openid, YTHEALTH_WX_OPENID_PREFIX) !== 0 && strpos($openid, TZJ_WX_OPENID_PREFIX) !== 0 && strpos($openid, ZY_WX_OPENID_PREFIX) !== 0 && strpos($openid, ICVD_WX_OPENID_PREFIX) !== 0 && strpos($openid, WX_OPENID_PREFIX_NEW) !== 0 && strpos($openid, WX_OPENID_PREFIX) !== 0) {
            $data['status'] = 1;
            // BAEQ-2460 STI 推送
            if ($check_info['org_id'] == STI_ORG_ID) {
                $fd16_sn = $check_info['ext_json']['sn'];
                $sti_sn = User::getEquipId($check_info['submit_user_id']);
                $patient = Patient::getPatientSelfById($check_info['patient_id'], true);
                STIHandler::sendMessage($fd16_sn, $sti_sn, 2, $patient['phone'], $check_info['check_id'], $check_info);
            }
        }
        //push_type = 11 30 分钟推送、12 60 分钟推送 4
        //push_type = 13 120分钟推送 SME-88
        //push_type = 14 40分钟推送 SME-137
        if ($check_info['push_type'] == 3) {
            // 延时推送15 分钟
            $data['status'] = 9;
        } elseif ($check_info['push_type'] == 4) {
            // 延时推送24小时
            $data['status'] = 10;
        } elseif ($check_info['push_type'] == 11) {
            $data['status'] = 11;
        } elseif ($check_info['push_type'] == 12) {
            $data['status'] = 12;
        } elseif ($check_info['push_type'] == 13) {
            $data['status'] = 13;
        } elseif ($check_info['push_type'] == 14) {
            $data['status'] = 14;
        }
        if ($check_info['image_quality'] == 3) {
            unset($data['status']);
        }
        // BAEQ-1331 本地医生审核后签字，但是还没审核
        if ($check_info['is_retina'] == 2 && $check_info['review_status'] == CheckInfo::REVIEW_DONE) {
            $data['status'] = 2;
        }
        $ret2 = WechatUserCheck::addItem($data, $repush);
        return $ret2;
    }

    static public function sendActivityByOpenId($item)
    {
        $openid = $item['open_id'];
        $whichPA = $item['whichPA'];
        $check_id = $item['check_id'];
        if (empty($openid)) {
            $pcode_item = PatientCode::getItemByCheckId($check_id);
            if ($pcode_item && $pcode_item['openid']) {
                $openid = $pcode_item['openid'];
            }
            if (empty($openid)) {
                $wechat_task_item = WechatUserCheck::getWechatByCheckId($check_id);
                if ($wechat_task_item && $wechat_task_item[count($wechat_task_item) - 1]['open_id']) {
                    $openid = $wechat_task_item[count($wechat_task_item) - 1]['open_id'];
                }
            }
        }
        if (empty($openid)) {
            \Phplib\Tools\Logger::error([$item], 'wechat_send_msg_error_baodao_unknow_openid');
            return;
        }

        $type = $item['type'];
        if ($whichPA == 2) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
            $template_id = WX_ACTIVITY_ID_NEW;
        } else {
            $obj = new WXUtil();
            $template_id = WX_ACTIVITY_ID;
        }
        $domain = defined('EYE_DOMAIN_HTTPS_PE') ? EYE_DOMAIN_HTTPS_PE : EYE_DOMAIN;
        $prize = '宝岛眼镜代金券';
        // if ($type == 1) {
        //     $prize = '宝岛眼镜96抵200代金券';
        // }
        // elseif ($type == 2) {
        //     $prize = '宝岛眼镜340抵500代金券';
        // }
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $domain . "h5/activities/baodao?open_id=" . $openid . "&whichPA=" . $whichPA,
            'data' => [
                'first' => [
                    'value' => '恭喜您成功领取宝岛眼镜代金券！',
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $prize,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => '全国1200家宝岛眼镜门店通用',
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => '即日起至2019年12月31日有效详情请点击。',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '',
                    'color' => '#173177'
                ]
            ]
        ];
        $result = $obj->pushMessage($data);
        if ($result === 0) {
            if ($obj->appId == WX_APPID) {
                $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
                $data['template_id'] = WX_ACTIVITY_ID_NEW;
                $whichPA = 2;
                $data['url'] = $domain . "h5/activities/baodao?open_id=" . $openid . "&whichPA=" . $whichPA;
            } else {
                $obj = new WXUtil(WX_APPID, WX_SECRET);
                $data['template_id'] = WX_ACTIVITY_ID;
                $whichPA = 1;
                $data['url'] = $domain . "h5/activities/baodao?open_id=" . $openid . "&whichPA=" . $whichPA;
            }
            $result = $obj->pushMessage($data);
        }
        if ($result === 0) {
            \Phplib\Tools\Logger::error([$item, $result], 'wechat_send_msg_error_baodao');
        }
        return $result;
    }

    public static function getYingtongUrl($checkinfo, $params = [], $id = '', $channel = 0)
    {
        if ($checkinfo['ext_json']['bisheng']) {
            $check_id = $checkinfo['check_id'];
            $BishengUtil = new BishengUtil();
            return $BishengUtil->getReportUrl($check_id, 'h5');
        }
        if ($checkinfo['ext_json']['report_type'] == 'yanzhijian') {
            $obj = new GlgHandler();
            return $obj->getGlgH5Url($checkinfo);
        }
        if (in_array($checkinfo['h5_template'], [1, 2])) {
            return self::getH5Url($checkinfo); //返回 1 药企，2 月子中心链接
        }
        if (!$id) {
            if (!$checkinfo['check_id']) {
                Logger::info(['getYingtongUrl check_id is empty', $checkinfo, $params], 'getyingurl_info');
            }
            $params['en_check_id'] = Xcrypt::encrypt($checkinfo['check_id']);
            // KMS超时，增加一次重试
            if ($checkinfo['check_id'] && !$params['en_check_id']) {
                $params['en_check_id'] = Xcrypt::encrypt($checkinfo['check_id']);
            }
            // KMS超时，脚本增加退出策略
            if ($checkinfo['check_id'] && !$params['en_check_id'] && defined('SCRIPT_CALL_NAME')) {
                exit();
            }
        }
        if ($checkinfo['ext_json'] && !is_array($checkinfo['ext_json'])) {
            $checkinfo['ext_json'] = json_decode($checkinfo['ext_json'], 1);
        }
        $params['is_yingtong'] = 1;
        $uri = 'icvd/report';

        if ($id) {
            $en_openid = $en_phone = '';
            if ($params['id_type'] == 'openid') {
                $en_openid = Xcrypt::encrypt($id);
            } else {
                $en_phone = Xcrypt::encryptNew($id);
            }
            $params = ['showNavigation' => $params['showNavigation']];
            $params['en_openid'] = $en_openid ? $en_openid : '';
            $params['en_phone'] = $en_phone ? $en_phone : '';
            $params['org_id'] = $checkinfo['org_id'];
            $uri = 'icvd/report/list';
            if ($channel) {
                $uri = 'thirdparty/report/list';
                $params['channel'] = $channel;
                unset($params['org_id']);
            }
        } elseif ($checkinfo['is_fd16'] == 1 && $checkinfo['patient']['status'] == 0 && defined('ICVD_ANLYZE') && ICVD_ANLYZE) {
            $uri = 'icvd/analyze';
            if ($checkinfo['retina_ex']['gender_ai']) {
                $params['gender_ai'] = $checkinfo['retina_ex']['gender_ai'];
                $params['gender_idx'] = $checkinfo['retina_ex']['gender_idx'];
                if ($checkinfo['retina_ex']['overall_age'] >= 10 || $checkinfo['retina_ex']['age'] >= 10) {
                    $params['age'] = $checkinfo['retina_ex']['overall_age'] ? $checkinfo['retina_ex']['overall_age'] : $checkinfo['retina_ex']['age'];
                    $params['age'] = min(round($params['age']), 99);
                }
            }
        } elseif ($checkinfo['is_fd16'] == 1 && $checkinfo['continuous_monitoring'] == 1 && $checkinfo['is_push_continuous_monitoring'] == 1) {
            $url = EYE_DOMAIN_HTTPS_PE . "h5-v2/continuousMonitoring?en_check_id=" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['driver_report'] == 2) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/driver?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['report_type'] == 'tizhijian_23') {
            $url = EYE_DOMAIN_HTTPS_PE . 'user/reportTzjC3m1/' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['report_type'] == 'tizhijian_24') {
            $url = EYE_DOMAIN_HTTPS_PE . 'user/reportTzj/' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['report_type'] == 'tizhijian_25') {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/youngEye?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['report_type'] == 'yt_health') {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/healthManagement/index?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif (in_array($checkinfo['ext_json']['new_template'], [20]) || in_array($checkinfo['new_template'], [20])) {
            //$url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/healthManagement/index?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            $sme_config_is_sme_org = array_key_exists('sme_config_is_sme_org', $checkinfo['ext_json']) ? intval($checkinfo['ext_json']['sme_config_is_sme_org']) : 0;

            if (!empty($checkinfo['ext_json']['is_oversea'])) {
                $url = EYE_DOMAIN_HTTPS_OVERSEA . 'h5/reportKs/' . urlencode(OverseaReportUtils::encryptNew($checkinfo['check_id'])) . '?language=' . OverseaReportUtils::getReportLang($checkinfo);
            } else {
                $sub_name = 'reportKs';
                if ($sme_config_is_sme_org > 1 && $sme_config_is_sme_org < 4) {
                    $sub_name = 'reportSme';
                }
                $url = EYE_DOMAIN_HTTPS_PE . "user/" . $sub_name . "/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));

                if ($sme_config_is_sme_org >= 4) {
                    $sme_config = new SMEConfig(intval($checkinfo['org_id']));
                    $sme_urls = $sme_config->getReportUrls($checkinfo['check_id']);

                    if ($sme_urls && is_array($sme_urls) && array_key_exists('data', $sme_urls) && array_key_exists('h5_url', $sme_urls['data'])) {
                        $url = $sme_urls['data']['h5_url'];
                    } else {
                        \Phplib\Tools\Logger::error(['check_id' => $checkinfo['check_id'], 'res' => $sme_urls], 'sme_connect');
                    }
                }

                $url = self::getUserReportUrl($checkinfo, $url);
            }
            return $url;
        } elseif ($checkinfo['ext_json']['woman_report']) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/ytReportWomen?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['child_report']) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/ytReportYoung?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['driver_report'] == 1) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/driverReport?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['annuity_report']) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/ytAnnuity?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif ($checkinfo['ext_json']['report_version'] == 7) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/ytReport?en_check_id=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif (in_array($checkinfo['org_id'], [SKB_ORG_ID_YT])) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/zy/report?is_ytskb=1&reportId=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            return $url;
        } elseif (!$checkinfo['is_yingtong'] && ($checkinfo['hxt_plus_agent'] || substr($checkinfo['patient']['uuid'], 0, 4) != ICVD_PCODE_PREFIX)) {
            $url = EYE_DOMAIN_HTTPS_PE . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            $url = self::getUserReportUrl($checkinfo, $url);
            return $url;
        } elseif (!$checkinfo['is_yingtong']) {
            $url = EYE_DOMAIN_HTTPS_PE . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            $url = self::getUserReportUrl($checkinfo, $url);
            return $url;
        }
        $query_string = http_build_query($params);
        $url = EYE_DOMAIN_HTTPS_PE . "{$uri}?{$query_string}";
        return $url;
    }
    public static function getH5Url($checkinfo)
    {
        if ($checkinfo['h5_template'] == 1) { //0.默认值 1.西安利君药企 2.月子中心
            return EYE_DOMAIN_HTTPS_PE . "lijun/Report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        } else { //0.默认值 1.西安利君药企 2.月子中心
            return EYE_DOMAIN_HTTPS_PE . "user/ccreport/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        }
    }

    //微信第三方开放平台wxkf处理
    public static function sendWxkfMsgByOpenId($item, $checkinfo, $from_qr = 0)
    {
        $openid = $item['open_id'];

        $wechat = Wechat::getRecordByOpenid($openid);
        $appid = $wechat['channel_num'];
        $wx_app_info = WxThird::getInfoByAppid($appid);
        $access_token = $wx_app_info['access_token'];
        $template_id = $wx_app_info['template_report'];

        $name = '健康评估报告';
        $title_first = $checkinfo['patient']['name'] . '您好，';
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = '您好，';
        }
        $title_first = $title_first . '您的报告已生成。';
        //$url = EYE_DOMAIN_HTTPS_PE . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        $url = self::getYingtongUrl($checkinfo);
        $remark = $checkinfo['patient']['status'] ? '查看报告' : '请补充个人信息查看完整报告';
        $org_id = $checkinfo['org_id'];
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  $item['check_id'],
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' =>  date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];
        $obj = new WXUtil();
        $data['access_token'] = $access_token;
        $result = $obj->pushMessage($data, $checkinfo);
        if ($item['id'] && $result) {
            self::updateStatus($item['id'], 1, $item['check_id']);
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            $from_qr && $event = 'wechat_push_success_from_qr';
            CheckLog::addLogInfo($item['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
        }
        if ($result == 0) {
            self::updateStatus($item['id'], 2, $item['check_id']);
        }
        return $result;
    }
    public static function sendYTHealthMsgByOpenId($item, $checkinfo, $from_qr = 0)
    {
        $check_id = $checkinfo['check_id'];
        $result = RetinaCheckItem::onGenerateReport($check_id);
        $openid = $item['open_id'];
        $name = '鹰瞳健康';
        $title_first = $checkinfo['patient']['name'] . '您好，';
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = '您好，';
        }
        $title_first = $title_first . '您的报告已生成。';
        //$url = EYE_DOMAIN_HTTPS_PE . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        $url = self::getYingtongUrl($checkinfo);
        $template_id = YTHEALTH_REPORT_NOTICE_TEMPLATE_ID;
        $remark = $checkinfo['patient']['status'] ? '点击查看报告' : '请补充个人信息查看完整报告';
        $org_id = $checkinfo['org_id'];


        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];
        $obj = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
        $result = $obj->pushMessage($data, $checkinfo);
        if ($item['id'] && $result) {
            self::updateStatus($item['id'], 1, $item['check_id']);
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            $from_qr && $event = 'wechat_push_success_from_qr';
            CheckLog::addLogInfo($item['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
        }
        if ($result == 0) {
            self::updateStatus($item['id'], 2, $item['check_id']);
        }
        if ($checkinfo['patient']['status'] && !$from_qr) {
            // notice agent
            $check_agents = DBCheckAgentMapHelper::getLines(['check_id' => $item['check_id']], true);
            if (!empty($check_agents)) {
                $url = AGENT_TOOL_DOMAIN . 'api/agent/receiveNotice';
                $params = [
                    'agent_num' => $check_agents[0]['agent_num'],
                    'name' => $checkinfo['patient']['name'],
                    'gender' => $checkinfo['patient']['gender'],
                    'type' => 'report_generate'
                ];
                Utilities::curl($url, $params, ['is_post' => 1, 'need_decode' => 1]);
            }
        }
        return $result;
    }
    public static function sendTZJMsgByOpenId($item, $checkinfo, $from_qr = 0)
    {
        $openid = $item['open_id'];
        $name = '体知健健康评估';
        $title_first = $checkinfo['patient']['name'] . '您好，';
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = '您好，';
        }
        $title_first = $title_first . '您的报告已生成。';
        //$url = EYE_DOMAIN_HTTPS_PE . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        $url = self::getYingtongUrl($checkinfo);
        $template_id = TZJ_REPORT_NOTICE_TEMPLATE_ID;
        $remark = $checkinfo['patient']['status'] ? '查看报告' : '请补充个人信息查看完整报告';
        $org_id = $checkinfo['org_id'];


        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];
        $obj = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
        $result = $obj->pushMessage($data, $checkinfo);
        if ($item['id'] && $result) {
            self::updateStatus($item['id'], 1, $item['check_id']);
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            $from_qr && $event = 'wechat_push_success_from_qr';
            CheckLog::addLogInfo($item['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
        }
        if ($result == 0) {
            self::updateStatus($item['id'], 2, $item['check_id']);
        }
        return $result;
    }
    public static function sendThirdMsgByOpenId($item, $checkinfo, $from_qr = 0, $wechat_config)
    {
        $openid = $item['open_id'];
        $name = $wechat_config['name'] . '健康评估';
        $title_first = $checkinfo['patient']['name'] . '您好，';
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = '您好，';
        }
        $title_first = $title_first . '您的报告已生成。';
        //$url = EYE_DOMAIN_HTTPS_PE . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        $url = self::getYingtongUrl($checkinfo);
        $template_id = $wechat_config['template'][2]['template_id'];;
        $remark = $checkinfo['patient']['status'] ? '查看报告' : '请补充个人信息查看完整报告';
        $org_id = $checkinfo['org_id'];
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];
        $obj = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
        $result = $obj->pushMessage($data, $checkinfo);
        if ($item['id'] && $result) {
            self::updateStatus($item['id'], 1, $item['check_id']);
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            $from_qr && $event = 'wechat_push_success_from_qr';
            CheckLog::addLogInfo($item['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
        }
        if ($result == 0) {
            self::updateStatus($item['id'], 2, $item['check_id']);
        }
        return $result;
    }
    public static function sendZYMsgByOpenId($item, $checkinfo, $from_qr = 0)
    {
        $openid = $item['open_id'];
        $name = '众佑健康评估';
        $title_first = $checkinfo['patient']['name'] . '您好，';
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = '您好，';
        }
        $title_first = $title_first . '您的众佑健康评估报告已生成。';
        //$url = EYE_DOMAIN_HTTPS_PE . "user/report/" . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        $url = self::getYingtongUrl($checkinfo);
        if (in_array($checkinfo['org_id'], ThirdHandler::ORG_IDS['baobei'])) {
            $url = sprintf(ThirdHandler::H5['baobei'][ENV], urlencode($url));
        } else {
            $url = self::formatUserReportUrl($url);
            $url .= "open_id=" . $openid;
        }
        if (in_array($checkinfo['org_id'], [SKB_ORG_ID, SKB_ORG_ID_YT])) {
            $url = EYE_DOMAIN_HTTPS_PE . 'h5-v2/zy/report?reportId=' . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
        }
        $template_id = ZY_REPORT_NOTICE_TEMPLATE_ID;
        $remark = $checkinfo['patient']['status'] ? '查看报告' : '请补充个人信息查看完整报告';
        $org_id = $checkinfo['org_id'];
        if ($org_id == PA_ZY_ORG_ID && in_array(PA_SWITCH, [1, 2])) {
            $check_order = CheckOrder::checkExistByPcode($checkinfo['patient']['uuid'], PA_ZY_ORG_ID);
            if ($check_order) {
                $not_push_config = 1;
            }
        } elseif ($org_id == PA_APP_ORG_ID && in_array(PA_APP_SWITCH, [1, 2])) {
            $check_order = CheckOrder::checkExistByPcode($checkinfo['patient']['uuid'], PA_APP_ORG_ID);
            if ($check_order) {
                $not_push_config = 1;
            }
        }
        $push_config = Organizer::getPushConfig($org_id);
        if ($push_config && !$from_qr && !$not_push_config) {
            // if ($push_config && ENV == 'production' && !$from_qr) {
            $remark = $push_config['template_message_remark'];
            if ($push_config['template_message_has_link']) {
                $discribe = Distribution::getSubscribe(['check_id' => $checkinfo['check_id']]);
                if (in_array($checkinfo['org_id'], [YEKIATAI_ORG_ID]) && empty($discribe)) {
                    # 不做处理
                    $url = $url;
                } else {
                    $url = $push_config['template_message_link'];
                }
            }
        }

        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];
        if ($push_config && $push_config['mini_appid'] && substr($push_config['mini_appid'], 0, 2) == 'wx' && !$from_qr) {
            $data['miniprogram'] = [
                'appid' => $push_config['mini_appid'],
                'pagepath' => '/',
            ];
            if (in_array($checkinfo['org_id'], ThirdHandler::ORG_IDS['gaoji'])) {
                $data['miniprogram']['pagepath'] = 'modules/airdoc/detail/index?check_id=' . $item['check_id'];
            }
            $data['url'] = '';
        }
        $obj = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
        $result = $obj->pushMessage($data, $checkinfo);
        if ($item['id'] && $result) {
            self::updateStatus($item['id'], 1, $item['check_id']);
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            $from_qr && $event = 'wechat_push_success_from_qr';
            CheckLog::addLogInfo($item['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
        }
        if ($result == 0) {
            self::updateStatus($item['id'], 2, $item['check_id']);
        }
        return $result;
    }

    public static function sendICVDMsgByOpenId($item, $checkinfo, $from_qr = 0)
    {
        $check_id = $checkinfo['check_id'];
        $result = RetinaCheckItem::onGenerateReport($check_id);
        if ($checkinfo['continuous_monitoring'] == 1 && $from_qr) {
            $checkinfo['is_push_continuous_monitoring'] = 1;
            if (CheckInfo::isContinuousMonitoring($result) == 1) {
                $checkinfo['is_push_continuous_monitoring'] = 0;
            }
        }
        $openid = $item['open_id'];
        $name = '心脑血管健康评估';
        $title_first = $checkinfo['patient']['name'] . '您好，';
        if ($checkinfo['patient']['status'] == 0) {
            $title_first = '您好，';
        }
        $url = self::getYingtongUrl($checkinfo);
        if (in_array($checkinfo['org_id'], [SKB_ORG_ID_YT])) {
            $title_first = $title_first . '您的鹰瞳健康报告已生成。';
            $name = '鹰瞳健康报告';
            //$url = self::getYingtongUrl($checkinfo);
        } elseif ($checkinfo['hxt_plus_agent'] || substr($checkinfo['patient']['uuid'], 0, 4) != ICVD_PCODE_PREFIX) {
            $name = '健康评估';
            $title_first = $title_first . '您的健康评估报告已生成。';
            //$url = '';
            //$url_report = $url . urlencode(Xcrypt::encrypt($checkinfo['check_id']));
            $url_report = self::formatUserReportUrl($url);
            $url = $url_report . "open_id=" . $openid;
        } else {
            $title_first = $title_first . '您的心脑血管健康评估报告已生成。';
            //$url = self::getYingtongUrl($checkinfo);
        }
        $template_id = ICVD_REPORT_NOTICE_TEMPLATE_ID;
        $remark = $checkinfo['patient']['status'] ? '查看报告' : '请补充个人信息查看完整报告';
        $org_id = $checkinfo['org_id'];
        $miniprogram = 0;
        if ($org_id == STI_ORG_ID) {
            $fd16_sn = $checkinfo['sn'];
            $sti_sn = User::getEquipId($checkinfo['submit_user_id']);
            $patient = Patient::getPatientSelfById($checkinfo['patient_id'], true);
            STIHandler::sendMessage($fd16_sn, $sti_sn, 2, $patient['phone'], $checkinfo['check_id'], $checkinfo);
        } else {
            $push_config = Organizer::getPushConfig($org_id);
            if ($push_config && $push_config['mini_appid'] && substr($push_config['mini_appid'], 0, 2) == 'wx' && !$from_qr) {
                $miniprogram = 1;
            } elseif ($push_config && !$from_qr) {
                $remark = $push_config['template_message_remark'];
                if ($push_config['template_message_has_link']) {
                    $url = $push_config['template_message_link'];
                } else {
                    $url = '';
                }
            }
        }
        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $url,
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $name,
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  date('Y年m月d日'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => $remark,
                    'color' => '#ff0000'
                ]
            ]
        ];
        if ($miniprogram) {
            $data['miniprogram'] = [
                'appid' => $push_config['mini_appid'],
                'pagepath' => '/',
            ];
            if (in_array($checkinfo['org_id'], TAIKANG_ORG_ID)) {
                $data['miniprogram']['pagepath'] = 'tpc/webview/webview?url=' . urlencode($data['url'] . '&title=' . urlencode('泰康鹰瞳报告'));
            }
            $data['url'] = '';
        }
        $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        $result = $obj->pushMessage($data, $checkinfo);
        if ($item['id'] && $result) {
            self::updateStatus($item['id'], 1, $item['check_id']);
            $event = 'wechat_push_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_success_again';
            }
            if (isset($checkinfo['push_health_advice']) && $checkinfo['push_health_advice'] && ($checkinfo['report_version'] || $checkinfo['annuity_report'])) {
                $health_advice_arr = [];
                $health_advice_arr['check_id'] = $item['check_id'];
                $health_advice_arr['open_id'] = $item['open_id'];
                $health_advice_arr['status'] = 0;
                if ($checkinfo['push_health_advice'] == 1) { //延时30分钟
                    $push_time = date('Y-m-d H:i:s', time() + 30 * 60);
                } elseif ($checkinfo['push_health_advice'] == 2) { //延时60分钟
                    $push_time = date('Y-m-d H:i:s', time() + 60 * 60);
                } elseif ($checkinfo['push_health_advice'] == 3) { //延时24小时
                    $push_time = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
                }
                $health_advice_arr['push_time'] = $push_time;
                WechatPushHealthAdvice::create($health_advice_arr);
            }

            $from_qr && $event = 'wechat_push_success_from_qr';
            CheckLog::addLogInfo($item['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]]);
        }
        if ($result == 0) {
            self::updateStatus($item['id'], 2, $item['check_id']);
        }
        if ($checkinfo['patient']['status'] && !$from_qr) {
            // notice agent
            $check_agents = DBCheckAgentMapHelper::getLines(['check_id' => $item['check_id']], true);
            if (!empty($check_agents)) {
                $url = AGENT_TOOL_DOMAIN . 'api/agent/receiveNotice';
                $params = [
                    'agent_num' => $check_agents[0]['agent_num'],
                    'name' => $checkinfo['patient']['name'],
                    'gender' => $checkinfo['patient']['gender'],
                    'type' => 'report_generate'
                ];
                Utilities::curl($url, $params, ['is_post' => 1, 'need_decode' => 1]);
            }
        }

        return $result;
    }

    public static function sendServiceProgressMsg($uuid, $sub_type_2 = '', $check_info = [])
    {
        //设置语言处理
        // Utilities::restoreI18n();
        if ($check_info) {
            if (!$check_info['org']) {
                if ($check_info['org_id'] > 0) {
                    $org_info = Organizer::getOrgSubsidiaryByIds($check_info['org_id']);
                    $org_info = $org_info[0];
                    $report_lang = $org_info['config']['report_lang'];
                }
            } else {
                $org_info = $check_info['org'];
                $report_lang = $org_info['config']['report_lang'];
            }
            if ($report_lang) {
                Utilities::setI18n($report_lang);
            }
        }

        $pcode_item = PatientCode::getItemByPcode($uuid);
        $is_hxt = strpos($uuid, '8996') === 0 ? 1 : 0;
        if ($pcode_item) {
            $openid = $pcode_item['openid'];
            $keyword1 = '鹰瞳健康';
            $keyword2 = '正在分析中';
            $remark = '报告生成后会通过现场工作人员或系统通知到您，请耐心等待。';
            $name = '';
            // TODO: 慧心瞳待做
            $sub_openid = substr($openid, 0, 5);
            // 微信公众号可配置 jira- 1421
            $wechat_config_data['prefix'] =  $sub_openid;
            $wechat_config_data['type'] = 1;
            $wechat_config = WechatThird::getWechatConfig($wechat_config_data);
            if ($wechat_config) {
                $wx_util = new WXUtil($wechat_config['appid'], $wechat_config['secret']);
                $template_id = $wechat_config['template'][4]['template_id'];
                $keyword1 = $wechat_config['name'] . '健康评估';
            } elseif (strpos($openid, YTHEALTH_WX_OPENID_PREFIX) === 0) {
                $wx_util = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
                $template_id = YTHEALTH_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID;
                $keyword1 = YTHEALTH_FULL_NAME;
            } elseif (strpos($openid, TZJ_WX_OPENID_PREFIX) === 0) {
                $wx_util = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
                $template_id = TZJ_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID;
                $keyword1 = TZJ_FULL_NAME;
            } elseif (strpos($openid, ICVD_WX_OPENID_PREFIX) === 0) {
                $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
                $template_id = ICVD_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID;
            } elseif (strpos($openid, ZY_WX_OPENID_PREFIX) === 0) {
                $patient = Patient::getPatientByUuid($uuid);
                $name = $patient['name'];
                $wx_util = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
                $template_id = ZY_IMAGE_UPLOADED_NOTICE_TEMPLATE_ID;
                // $is_zhongyou = 1;
                $keyword1 = '众佑健康评估';
            } elseif (strpos($openid, WX_OPENID_PREFIX) === 0 && (date('H') >= '23' || date('H') < '07')) {
                // 慧星瞳夜间提醒
                $wx_util = new WXUtil(WX_APPID, WX_SECRET);
                $template_id = WX_NIGHT_DELAY_NOTICE_TEMPLATE_ID;
                $keyword1 = '慧心瞳';
                if ($sub_type_2 && strpos('pre' . $sub_type_2, 'yt_medical_icvd')) {
                    $keyword1 = '鹰瞳医疗-ICVD';
                    return FALSE; // BAEQ-3831 4.7 DP
                } elseif ($sub_type_2 && strpos('pre' . $sub_type_2, 'yt_medical_mv')) {
                    $keyword1 = '鹰瞳医疗-MV';
                    return FALSE; // BAEQ-3831 4.7 DP
                } elseif ($sub_type_2 && strpos('pre' . $sub_type_2, 'yt_medical')) {
                    $keyword1 = '鹰瞳医疗';
                }
                $keyword2 = '等待分析';
                $remark = '23:00～7:00为系统维护时间，您的报告将顺延至7:00后生成';
            } elseif ($pcode_item['new_wechat'] == 6) { //微信第三方wxkf 报告分析中消息处理
                $wx_util = new WXUtil();
                $wechat = Wechat::getRecordByOpenid($openid);
                $appid = $wechat['channel_num'];
                $wx_app_info = WxThird::getInfoByAppid($appid);
                $access_token = $wx_app_info['access_token'];
                $template_id = $wx_app_info['template_id'];
                $keyword1 = '健康评估报告';
                if (!$access_token) {
                    \Phplib\Tools\Logger::error(['event' => 'wxkf access_token empty]', 'data' => ['uuid' => $uuid, 'openid' => $openid]], 'wechat_send_progress_msg_error');
                    return FALSE;
                }
            } else {
                return FALSE;
            }

            $date_format = getenv('LANG') ? Utilities::getLocaleDateFormat(getenv('LANG'), 'ymd') : 'Y年m月d日';

            $data = [
                'touser' => $openid,
                'template_id' => $template_id,
                'url' => '',
                'data' => [
                    'first' => [
                        'value' => $name . gettext('资料已上传成功'),
                        'color' => '#173177'
                    ],
                    'keyword1' => [
                        'value' => gettext($keyword1), //$is_zhongyou ? '众佑健康评估' : '鹰瞳健康',
                        'color' => '#173177'
                    ],
                    'keyword2' => [
                        'value' =>  gettext($keyword2), //'正在分析中',
                        'color' => '#ff0000'
                    ],
                    'keyword3' => [
                        'value' =>  date($date_format),
                        'color' => '#173177'
                    ],
                    'remark' => [
                        'value' => gettext($remark),
                        'color' => '#173177'
                    ]
                ]
            ];
            if (in_array($pcode_item['org_id'], [SKB_ORG_ID_YT])) {
                $data['data']['keyword1']['value'] = '鹰瞳健康';
            }
            if ($pcode_item['new_wechat'] == 6 && $access_token) { //微信第三方wxkf 报告分析中消息处理
                $data['access_token'] = $access_token;
            }
            $result = $wx_util->pushMessage($data);
            if ($result === 0) {
                \Phplib\Tools\Logger::error('pcode=' . $uuid, 'wechat_send_progress_msg_error');
            }
        }
    }
    static public function sendVoiceByOpenId($openid, $item)
    {
        $title_first = '以成功为您分配专属智能报告解读服务, 您可随时通过此链接查看解读内容';
        $data = [
            'touser' => $openid,
            'template_id' => ICVD_VOICE, //模版id
            'data' => [
                'first' => [
                    'value' => $title_first,
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $item['name'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $item['gender'],
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' =>  '隐私保护',
                    'color' => '#173177'
                ],
                'keyword4' => [
                    'value' => '隐私保护',
                    'color' => '#173177'
                ],
                'keyword5' => [
                    'value' => date('Y-m-d H:i:s'),
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '点击此处进行智能语音解读',
                    'color' => '#ff0000'
                ]
            ]
        ];
        $data['url'] = $item['url'];
        Logger::info($data, 'voice', []);
        $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        $result = $wx_util->pushMessage($data);
        if (!$result) {
            Utilities::DDMonitor("P3-voice-智能语音解读模版消息推送失败 ,失败原因:" . json_encode($result), 'cloudm', TRUE);
            Logger::info($result, 'voice');
        }
        return $result;
    }

    public static function sendRecallMessage($openid, $check_id, $batch_num, $bucket = 0)
    {
        if (strpos($openid, ICVD_WX_OPENID_PREFIX) === 0) {
            $keyword1 = '您的身体存在%s情况，需要持续关注';
            $keyword2 = '距离您上次进行鹰瞳健康风险评估已经过去%s天';
            $cobj = new CheckInfo();
            $recall_data = $cobj->getRecallData($check_id);
            $keyword1 = sprintf($keyword1, $recall_data['risk_name']);
            $keyword2 = sprintf($keyword2, $recall_data['past_days']);
            $remark = "为了防止您的风险增加，强烈建议您再次进行评估，点击预约！\n如果您不想接收此提醒，请联系我们的客服取消";
            $wx_util = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
            $template_id = ICVD_REVIEW_NOTICE_TEMPLATE_ID;
            if ($bucket == 0) {
                $check_infos = $cobj->getCheckInfoByIds($check_id);
                $url = self::getYingtongUrl($check_infos[0]);
                $params = '?openid=' . $openid . '&en_check_id=' . urlencode(Xcrypt::encrypt($check_id)) . '&batch_num=' . $batch_num . '&is_yingtong=1';
            }
            $data = [
                'touser' => $openid,
                'template_id' => $template_id,
                'data' => [
                    'first' => [
                        'value' => '健康风险评估邀请',
                        'color' => '#173177'
                    ],
                    'content' => [
                        'value' => $keyword1,
                        'color' => '#ff0000'
                    ],
                    'occurtime' => [
                        'value' =>  $keyword2,
                        'color' => '#173177'
                    ],
                    'remark' => [
                        'value' => $remark,
                        'color' => '#173177'
                    ]
                ]
            ];
            if ($bucket == 0) {
                $data['miniprogram'] = [
                    'appid' => REGISTER_WX_APPID,
                    'pagepath' => 'pages/reviewAgreement/reviewAgreement' . $params
                ];
            } else {
                $data['url'] = EYE_DOMAIN_HTTPS_PE . 'h5-v2/checkAgain';
            }
            $result = $wx_util->pushMessage($data);
            if ($result) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * 慧心瞳夜间维护提醒
     * , $new_wechat = 0
     */
    // public static function sendNightDelayMsgByOpenId($openid)
    // {
    //     if ($openid) {
    //         // $wx_util = $new_wechat ? new WXUtil(WX_APPID_NEW, WX_SECRET_NEW) : new WXUtil(WX_APPID, WX_SECRET);
    //         // $template_id = $new_wechat ? WX_NIGHT_DELAY_NOTICE_TEMPLATE_ID_NEW : WX_REGISTER_TEMPLATE_ID;
    //         if (strpos($openid, WX_OPENID_PREFIX) === 0) {
    //             $wx_util = new WXUtil(WX_APPID, WX_SECRET);
    //             $template_id = WX_NIGHT_DELAY_NOTICE_TEMPLATE_ID;
    //         } else {
    //             return FALSE;
    //         }
    //         \Phplib\Tools\Logger::error(['template_id' => $template_id, 'is_new_wx' => IS_NEW_WX], 'click_push_debug');
    //         $data = [
    //             'touser' => $openid,
    //             'template_id' => $template_id,
    //             'url' => '',
    //             'data' => [
    //                 'first' => [
    //                     'value' => '资料已上传成功',
    //                     'color' => '#173177'
    //                 ],
    //                 'keyword1' => [
    //                     'value' => '慧心瞳',
    //                     'color' => '#173177'
    //                 ],
    //                 'keyword2' => [
    //                     'value' =>  '等待分析',
    //                     'color' => '#173177'
    //                 ],
    //                 'keyword3' => [
    //                     'value' =>  date('Y年m月d日'),
    //                     'color' => '#173177'
    //                 ],
    //                 'remark' => [
    //                     'value' => '23:00～7:00为系统维护时间，您的报告将顺延至7:00后生成',
    //                     'color' => '#ff0000'
    //                 ]
    //             ]
    //         ];
    //         $result = $wx_util->pushMessage($data);
    //         if (isset($result['errcode']) && $result['errcode'] !== 0) {
    //             Utilities::DDMonitor("P3-night-慧心瞳夜间维护提醒:" . json_encode($result), 'cloudm', TRUE);
    //             \Phplib\Tools\Logger::error('open_id=' . $openid, 'wechat_send_progress_msg_error');
    //             return false;
    //         }
    //         return true;
    //     }
    // }
    static public function getUserReportUrl($check_info, $url)
    {
        if ($check_info['is_zhongyou'] && $check_info['business_line'] == 2) {
            if (stripos($url, 'user/report') !== false) {
                if (stripos($url, '?') !== false) {
                    $url = str_replace('?', '&', $url);
                }
                $url = str_replace('user/report/', 'h5-v2/zy/zyReport?en_check_id=', $url);
            }
        }
        // BAEQ-2745-EN
        if ($check_info['is_huixintong'] && $check_info['language'] == 'en_US') {
            if (stripos($url, '?') !== false) {
                $url .= '&language=en_US';
            } else {
                $url .= '?language=en_US';
            }
        }
        return $url;
    }

    static public function formatUserReportUrl($url)
    {
        if (stripos($url, '?') !== false) {
            $url .= "&";
        } else {
            $url .= "?";
        }
        return $url;
    }
    public static function sendHealthAdviceMsgByOpenId($item)
    {

        $openid = $item['open_id'];
        $template_id = ICVD_HEALTH_ADVICE_TEMPLATE_ID;
        if ($item['content']['level'] == 1) {
            $result = '您的身体存在中风险情况，请积极关注';
        } elseif ($item['content']['level'] == 2) {
            $result = '您的身体存在高风险情况，高风险不代表已经确诊，但您需要重视';
        } else {
            $result = '您的身体比较健康，请注意保持哦';
        }

        $data = [
            'touser' => $openid,
            'template_id' => $template_id,
            'url' => $item['content']['url'],
            'data' => [
                'first' => [
                    'value' => $item['content']['name'] . '健康建议',
                    'color' => '#173177'
                ],
                'keyword1' => [
                    'value' => $item['created'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' =>  $result,
                    'color' => '#173177'
                ],
                'keyword3' => [
                    'value' => '请点击查看您的专属健康建议',
                    'color' => '#173177'
                ],
                'remark' => [
                    'value' => '',
                    'color' => '#ff0000'
                ]
            ]
        ];
        $obj = new WXUtil(ICVD_WX_APPID, ICVD_WX_SECRET);
        $checkinfo['check_id'] = $item['check_id'];
        $result = $obj->pushMessage($data, $checkinfo);
        if ($item['id'] && $result) {
            DBWechatUserHealthAdviceHelper::updateLine(['id' => $item['id'], 'status' => 1]);
            $event = 'wechat_push_health_advice_success';
            if ($item['status'] == 3) {
                $event = 'wechat_push_health_advice_success_again';
            }
            CheckLog::addLogInfo($item['check_id'], $event, ['data' => ['param' => $data, 'result' => $result]], -1);
        }
        if ($result == 0) {
            DBWechatUserHealthAdviceHelper::updateLine(['id' => $item['id'], 'status' => 2]);
        }
        return $result;
    }
}
