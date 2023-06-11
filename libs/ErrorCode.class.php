<?php

namespace Air\Libs;

/**
 * 接口错误码定义
 * 格式： XXXxx
 * 说明：前三位具体对象，后二位为具体错误类型
 * 举例: 17001, 170标识check_info, 01为不存在错误
 */
class ErrorCode {

    // 成功只有一种，但是失败会有各种形式
    static public $SUCCESS = 0;
    // Order Error Code
    static public $OrderDoesNotExist = 10040;
    // Camera Error Code
    static public $CameraStoppedError = 12001;
    static public $CameraENVError = 12002;
    static public $CameraAccountNotFoundError = 12003;
    // 微信返回错误
    static public $WXResError = 13001;
    // 机构不匹配
    static public $UnMatchedOrgError = 14001;
    // 权限不匹配或者无权限
    static public $PermissionError = 14003;
    // 请求参数错误
    static public $ReqParamsError = 15001;

    // === CheckInfo 错误码 ===
    // 检查单不存在
    static public $CheckInfoNotExistsError = 17001;
    // =======================

    // === Org 类错误码 ===
    // Org不存在
    static public $OrgNotExistsError = 18001;
    // =======================

    // === OAuth对接类 ===
    // 未做系统对接
    static public $OAuthNotFoundError = 16001;
    // 客户服务器返回错误
    static public $RemoteServeError = 16002;
    // ==================



    static public $prefix = array(
        //人员（医生、内部人员） user
        'user/login'                        => 1001,
        'user/register'                     => 1002,
        'user/logout'                       => 1003,
        'user/verify'                       => 1004,
        'user/info'                         => 1007,
        'user/qiniutoken'                   => 1008,


        //报告 check
        'checklist/add'                     => 1100,
        'checklist/stats_base'              => 1101,
        'checklist/records'                 => 1102,
        'checklist/detail'                  => 1103,
        'checklist/update'                  => 1104,
        'checklist/tod'                     => 1105,
        'checklist/mylist'                  => 1106,
        'checklist/detail_diagnose'         => 1107,
        'checklist/stats'                   => 1108,
        'checklist/goback'                  => 1109,
        'checklist/disease_list'            => 1110,
        'checklist/reason_list'             => 1111,

        //患者 patient
        'patient/search'                    => 1300,

        //疾病标签相关  diagnose、disease、symptom
        'symptom/search'                    => 1401,
        'symptom/qlist'                     => 1402,
        'diagnose/group_disease_word_list'  => 1403,

        // 报告
        'report/base_info'                  => 5001,

        //开源接口 openapi
        'openapi/auto_add'                  => 8000,
        'openapi/check_params'              => 8001,

        //账号中心接口
        'user_center/get_user_list'         => 6000,
        'user_center/get_org_list'          => 6001,
        'user_center/user_login'            => 6002,
        'user_center/search_organizer'      => 6003,
        'user_center/search_user'           => 6004,
        'user_center/get_balance'           => 6005,
        'user_center/minus_balance'         => 6006,


        //其他接口
        'cash/bill_list'                    => 9000,



    );
    static public function prefix($path)
    {
        $path = substr($path, 4);
        if (isset(self::$prefix[$path])) {
            return self::$prefix[$path];
        } else {
            return 9000;
        }
    }
}