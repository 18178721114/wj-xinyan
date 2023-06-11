<?php
//获取地址

namespace Air\Libs;

use Air\Libs\Base\Utilities;

class GetAddress
{
    public static function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    public static function getAdress($ip)
    {
        $url = "https://api.live.bilibili.com/client/v1/Ip/getInfoNew";
        $url1 = "https://api.map.baidu.com/location/ip?ak=SsApPtKsixohYlgLoKdAUYBRwaCcHhQn&ip=$ip&coor=gcj02";
        // 先通过ip 获取 
        $result = [];
        if ($ip) {
            for ($i = 0; $i < 3; $i++) {
                $addr_ip = Utilities::curl($url1);
                if ($addr_ip['status'] == 0) {
                    $result['province'] = $addr_ip['content']['address_detail']['province'];
                    $result['city'] = $addr_ip['content']['address_detail']['city'];
                    return $result;
                }
            }
        }
        // for ($j = 0; $j < 3; $j++) {
        //     $url = 'https://api.live.bilibili.com/client/v1/Ip/getInfoNew';
        //     $addr = Utilities::curl($url);
        //     if ($addr['code'] == 0 && $addr['data'] != '') {
        //         $result['province'] = $addr['data']['province'];
        //         $result['city'] = $addr['data']['city'];
        //         return $result;
        //     }
        // }
        return [];
    }
}
