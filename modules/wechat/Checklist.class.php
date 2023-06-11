<?php

namespace Air\Modules\Wechat;

use Air\Libs\Xcrypt;
use Air\Libs\Base\Utilities;
use Air\Package\Checklist\CheckInfo;
use Air\Package\Checklist\CheckInfoUtil;
use Air\Package\Checklist\Helper\RedisLock;
use Air\Package\Checklist\CheckPdfMap;
use Air\Package\User\Organizer;
use Air\Package\Wechat\WechatUserCheck;
use Air\Package\Wechat\Wechat;
use Air\Package\Wechat\WechatThird;
use Air\Package\Wechat\WXUtil;
use Phplib\Tools\Logger;

class Checklist extends \Air\Libs\Controller
{
    public function run()
    {
        $request = $this->request;
        $openid = $request->REQUEST['openid'];
        if (empty($openid)) {
            $code = $this->request->REQUEST['code'];
            if ($request->REQUEST['is_yt_health'] == 1) {
                $wx_util = new WXUtil(YTHEALTH_WX_APPID, YTHEALTH_WX_SECRET);
            } elseif ($request->REQUEST['is_tizhijian'] == 1) {
                $wx_util = new WXUtil(TZJ_WX_APPID, TZJ_WX_SECRET);
            } elseif ($request->REQUEST['is_new'] == 1) {
                $wx_util = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
            } elseif ($request->REQUEST['is_new'] == 3) {
                $wx_util = new WXUtil(ZY_WX_APPID, ZY_WX_SECRET);
            } else {
                $wx_util = new WXUtil(WX_APPID, WX_SECRET);
            }
            $result = $wx_util->getAuthAccessToken($code);
            $openid = $result['openid'];
            $info = Wechat::getRecordByOpenid($openid);
            if (empty($info) && $openid) {
                $user_info = $wx_util->getUserInfo($result['access_token'], $openid);
                if ($user_info['nickname']) {
                    $id = Wechat::addRecord([
                        'openid' => $openid,
                        'nickname' => $user_info['nickname'],
                        'sex' => $user_info['sex'],
                        'city' => $user_info['city'],
                        'province' => $user_info['province'],
                        'country' => $user_info['country'],
                        'headimgurl' => $user_info['headimgurl'],
                        'wechat_type' => (int) $request->REQUEST['is_new'],
                    ]);
                } else {
                    \Phplib\Tools\Logger::error($user_info, 'wechat_jump');
                }
            }
            \Phplib\Tools\Logger::error($code, 'wechat_jump');
            \Phplib\Tools\Logger::error($result, 'wechat_jump');
            $url_suffix = intval($request->REQUEST['isXinGuan']) > 0 ? '&isXinGuan=1' : '';
            header("Location: " . EYE_DOMAIN_HTTPS_PE . "wechat/records?openid=" . $result['openid'] . $url_suffix);
            exit;
        }
        $wechat_user_check = new WechatUserCheck();
        $ret = $wechat_user_check->getByOpenid($openid, intval($this->request->REQUEST['page']));
        if (!$ret) {
            $this->setView(0, '', []);
            return FALSE;
        }
        $ak_check_ids = [];
        $yw_check_ids = [];
        foreach ($ret as $item) {
            if ($item['platform'] == 1) {
                $yw_check_ids[] = $item['check_id'];
            } else {
                $ak_check_ids[] = $item['check_id'];
            }
        }
        $ak_check_ids = array_unique($ak_check_ids);
        $yw_check_ids = array_unique($yw_check_ids);
        $cobj = new CheckInfo();
        $cobj->setNotQueryH5EntryData();
        $ak_checklist = $cobj->getCheckInfoByIds($ak_check_ids);
        $yw_checklist = $cobj->getYWCheckInfoByIds($yw_check_ids);
        $data = [];
        $jump_url = '';
        $orgs = Organizer::getPushConfigOrgs();
        foreach ($ak_checklist as $item) {
            if (in_array($item['review_status'], [0, 1, 50])) {
                continue;
            }
            if (in_array($item['org_id'], TAIKANG_ZY_ORG_ID)) {
                continue;
            }
            list($can_push_report, $can_not_push_report_reasons) = CheckInfoUtil::canPushReport($item);
            if (!$can_push_report) {
                $lock = RedisLock::lock('can_not_push_report_checklist_' . $item['check_id'], 60);
                if ($lock) {
                    $content = '未评估完成，原因【' . implode(',', $can_not_push_report_reasons) . '】 上传时间：' . $item['created'] . ' 开始评估时间：' . $item['start_time'];
                    Logger::info($content, 'can_not_push_report', ['check_id' => $item['check_id']]);
                };
                continue;
            }
            $new_item = [];
            $new_item['check_id'] = $item['check_id'];
            $new_item['encrypt_id'] = Xcrypt::encrypt($item['check_id']);
            if ($item['org_id'] == FD16_ORG_ID && strtolower($item['submit_user_name']) != 'airdoc') {
                $new_item['url'] = EYE_DOMAIN_HTTPS_PE . 'self/report/' . urlencode(Xcrypt::encrypt($item['check_id'])) . '?from_h5';
            } elseif ($item['push_report_type'] == 'pdf') {
                $pdf = CheckPdfMap::getPdfByCheckId($item['check_id']);
                if (!$pdf) {
                    continue;
                }
                $new_item['url'] = $pdf['pdf_url_signed'];
            } elseif (strpos($openid, WX_OPENID_PREFIX) === 0 && $orgs[$item['org_id']] && $orgs[$item['org_id']]['config'] && $orgs[$item['org_id']]['config']['template_message_link']) {
                $new_item['url'] = $orgs[$item['org_id']]['config']['template_message_link'];
            } else {
                $new_item_url = WechatUserCheck::getYingtongUrl($item);
                $new_item_url = WechatUserCheck::formatUserReportUrl($new_item_url);
                $new_item_url .= 'isXinGuan=' . intval($request->REQUEST['isXinGuan']);;
            }
            $level = 0;
            foreach ($item['ikang_wording_' . $item['package_type']]['description'] as $it) {
                if ($it['suggestion_level'] > $level) {
                    $level = $it['suggestion_level'];
                }
            }
            $new_item['severity'] = 0;
            if ($level > 2) {
                $new_item['severity'] = 2;
            } elseif ($level <= 2 && $level >= 1) {
                $new_item['severity'] = 1;
            }
            if ($item['is_tizhijian'] || in_array($item['ext_json']['report_type'], ["yt_health", 'yt_medical'])) {
                $new_item['url'] = WechatUserCheck::getYingtongUrl($item);
            }
            $new_item['created'] = $item['created'];
            $new_item['org_id'] = $item['org_id'];
            $new_item['is_zhongyou'] = $item['is_zhongyou'];
            $new_item['is_tizhijian'] = (int)$item['is_tizhijian'];
            $new_item['h5_status'] = $item['h5_status'];
            $new_item['date'] = Utilities::timeStrConverter(strtotime($item['created']));
            $new_item['suggestion'] = $item['ikang_wording_' . $item['package_type']]['suggestion'];
            $new_item['patient'] = [
                'name' => $item['patient']['name'],
                'gender' => $item['patient']['gender'],
                'age' => $item['patient']['age'],
            ];
            $data[] = $new_item;
            // 有引导页，众佑，没看过h5，七天内：跳转引导页
            if ($orgs[$item['org_id']]['config']['template_message_link'] && $item['is_zhongyou'] && !$item['h5_status'] && strtotime($item['start_time']) > time() - 7 * 86400) {
                $jump_url = trim($orgs[$item['org_id']]['config']['template_message_link']);
                $jump_check_id = $item['check_id'];
            }
        }
        foreach ($yw_checklist as $item) {
            $new_item = [];
            $new_item['check_id'] = $item['check_id'];
            $new_item['encrypt_id'] = Xcrypt::encrypt($item['check_id']);
            $new_item['severity'] = $item['severity'];
            $new_item['url'] = $item['url'];
            $new_item['created'] = $item['created'];
            $new_item['date'] = Utilities::timeStrConverter(strtotime($item['created']));
            $new_item['patient'] = [
                'name' => $item['patient']['name'],
                'gender' => $item['patient']['gender'],
                'age' => $item['patient']['age'],
            ];
            $data[] = $new_item;
        }
        $data = Utilities::sortArray($data, 'created', 'DESC');
        $result = [];
        // 只有一个检查，有引导页，众佑，没看过h5：跳转引导页
        if (count($data) == 1 && $orgs[$data[0]['org_id']]['config']['template_message_link'] && $data[0]['is_zhongyou'] && !$data[0]['h5_status']) {
            $result[0]['url'] = trim($orgs[$data[0]['org_id']]['config']['template_message_link']);
        } elseif ($jump_url) {
            $result[0]['url'] = $jump_url;
            $result[0]['jump_check_id'] = $jump_check_id;
        } else {
            $result = $data;
        }
        $this->setView(0, '', $result);
    }
}
