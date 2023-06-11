<?php

namespace Air\Modules\Wechat;


use Air\Package\Wechat\WXUtil;

class Get_qr_image extends \Air\Libs\Controller
{
    static private $ips = ['36.112.64.2', '116.247.81.186'];

    public function run()
    {
        $ip = $this->request->ip;
        if (!in_array($ip, self::$ips)) {
            $this->setView(2, 'IP invalid', $ip);
            //return FALSE;
        }
        $request = $this->request;
        if ($request->REQUEST['new']) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
        } else {
            $obj = new WXUtil(WX_APPID, WX_SECRET);
        }

        //需要职场的保险
        // $url['new']['insurance_default'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE");
        // $url['new']['insurance_B'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_B");
        // $url['new']['insurance_C'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_C");
        // $url['new']['insurance_D'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_D");
        // $url['new']['insurance_E'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_E");
        // $url['new']['insurance_G'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_G");
        //新保险
        // $url['new']['insurance_B_v2'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_B");
        // $url['new']['insurance_C_v2'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_C");
        // $url['new']['insurance_D_v2'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_D");
        // $url['new']['insurance_E_v2'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_E");
        // $url['new']['insurance_G_v2'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_G");
        //云南太平保险
        // $url['new']['insurance_G_v3'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V3_G");
        // $url['new']['insurance_B_v2_simple'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_B_SIMPLE");
        // $url['new']['insurance_C_v2_simple'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_C_SIMPLE");
        // $url['new']['insurance_D_v2_simple'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_D_SIMPLE");
        // $url['new']['insurance_E_v2_simple'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_E_SIMPLE");
        // $url['new']['insurance_G_v2_simple'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_G_SIMPLE");
        // $url['new']['insurance_B_v2_required_ID'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_B_ID");
        // $url['new']['insurance_C_v2_required_ID'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_C_ID");
        // $url['new']['insurance_D_v2_required_ID'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_D_ID");
        // $url['new']['insurance_E_v2_required_ID'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_E_ID");
        // $url['new']['insurance_G_v2_required_ID'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V2_G_ID");
        // $url['new']['insurance_G_v3_required_ID'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_INSURANCE_V3_G_ID");
        //先补充信息
        $url['new']['default'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW");
        $url['new']['pk_B'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWB");
        $url['new']['pk_C'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWC");
        $url['new']['pk_D'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWD");
        $url['new']['pk_D1'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWE");
        $url['new']['pk_chronic'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWG");
        $url['new']['pk_B_bv'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWB_BV");
        $url['new']['pk_B_Tibet'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWB_BV_TIBET");
        $url['new']['pk_B_dr'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWB_DR");
        //不需要身份证版本
        $url['new']['pk_B_noid'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWB_NOID");
        $url['new']['pk_C_noid'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWC_NOID");
        $url['new']['pk_D_noid'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWD_NOID");
        $url['new']['pk_E_noid'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWE_NOID");
        $url['new']['pk_chronic_noid'] = $obj->createChannelQRCode("IVAK_GETCODE_NEWG_NOID");
        $url['new']['hospital'] = $obj->createChannelQRCode("IVAK_GETCODE_HOSPITAL");

        // BAEQ-3130
        $url['new']['ikang_ty_身份证'] = $obj->createChannelQRCode("IVAK_GETCODE_TIYAN_ID");
        $url['new']['ikang_ty_没有身份证'] = $obj->createChannelQRCode("IVAK_GETCODE_TIYAN_NOID");
        //先补充信息-不推送
        //$url['new']['default_notpush'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_NOTPUSH");
        //$url['new']['pk_notpush_B'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_NOTPUSH_B");
        //$url['new']['pk_notpush_C'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_NOTPUSH_C");
        //$url['new']['pk_notpush_D'] = $obj->createChannelQRCode("IVAK_GETCODE_NEW_NOTPUSH_D");
        //后补充信息
        //$url['old']['default'] = $obj->createChannelQRCode("IVAK_GETCODE");
        //$url['old']['pk_B'] = $obj->createChannelQRCode("IVAK_GETCODE_B");
        //$url['old']['pk_C'] = $obj->createChannelQRCode("IVAK_GETCODE_C");
        //$url['old']['pk_D'] = $obj->createChannelQRCode("IVAK_GETCODE_D");
        $url['new']['shanghai_meternity_and_infant_hospital'] = $obj->createChannelQRCode("IVAK_GETCODE_HOSPITAL_ORG_40104");
        $url['new']['shandong_meternity_and_infant_hospital'] = $obj->createChannelQRCode("IVAK_GETCODE_HOSPITAL_ORG_40143");
        $this->setView(0, '', $url);
    }
}
