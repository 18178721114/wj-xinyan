<?php
namespace Air\Unit\Pingan;

use Air\Package\Ads\AdsUtil;

class SortAds
{
    public function __construct($arg)
    {
        $this->args = $arg;
        $this->case_id = $arg[0];
        $this->case_func = 'testCase' . $this->case_id;
    }
    public function run()
    {
        $result = [];
        $case_func = $this->case_func;
        $result = $this->$case_func();
        echo $result.PHP_EOL;
        echo "Bye\n";
        
        
        
        
        
    }

    public function testCase1()
    {
        $ads = [
            [
                "name"=>"众佑平安",
                "images" => "img1.png,img2.png,img3.png",
                "url" => "https://product.health.pingan.com/common-pages/notice.html?g=A100000082&c=YTYL,https://product.health.pingan.com/common-pages/notice.html?g=AM000000049&c=YTYL,https://product.health.pingan.com/common-pages/notice.html?g=AM000000050&c=YTYL"
            ]
        ];
        $now = date('Y-m-d H:i:s',time());
        for ($i = 0; $i<60; $i++) {
                $year = date("Y-m-d", strtotime("-{$i} years", strtotime($now)));
                $check_info = [
                    "check_id" => 1,
                    "third_channel" => 1,
                    "patient" =>[
                        "uuid"=> 2,
                        "birthday" => $year
                    ]
                ];
                $re = AdsUtil::handlePinganAds($ads, $check_info);
                echo $year.'-'.$re[0]['image'].'-'.$re[1]['image'].'-'.$re[2]['image'].PHP_EOL;
        }
        
    }

    public function testCase2()
    {
        $ads = [
            [
                "name"=>"小润video",
                "images" => "img1.png,img2.png,img3.png,img4.png",
                "url" => "mp4-1,mp4-2,mp4-3,mp4-4"
            ]
        ];
        $new_risky = [
            [
                "idx" => "anemia",
                "risky_level" => 1,
                //"name" => "血红蛋白不足风险"
            ],
            [
                "idx" => "dr",
                "risky_level" => 1,
                //"name" => "糖代谢风险"
            ],
            [
                "idx" => "heart",
                "risky_level" => 1,
                //"name" => "心血管风险"
            ],
            [
                "idx" => "brain",
                "risky_level" => 1,
                //"name" => "脑血管风险"
            ]
            
        ];
        $check_info['new_risky'] = $new_risky;
        foreach ($check_info['new_risky'] as $k => $item) {
            for ($i = 1; $i<=3; $i++) {
                $check_info['new_risky'][$k]['risky_level'] = $i;
                $re = AdsUtil::handleXiaorunVideo($ads, $check_info);
                echo $re['url'].json_encode($check_info['new_risky']). PHP_EOL;

            }
            

            
        }
        
        
    }
}