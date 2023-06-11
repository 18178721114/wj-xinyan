<?php
namespace Air\Libs;

class DingTalkRobot
{
    /**
     * @param $subject
     * @param $to
     * @param $msg
     * @param string $attach
     * @param string $content_type
     * @return int
     */
    public static function sendText($url, $content, $at=[])
    {
        $data['msgtype'] = 'text';
        $data['text'] = ['content' => $content];
        if (empty($at)) {
            $data['at'] = ['isAtAll' => true];
        } else {
            $data['at'] = ['atMobiles' => $at];
        }
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);                
        return $data;  
    }

    public static function sendLink($url, $title, $content, $pic_url, $message_url)
    {
        $data['msgtype'] = 'link';
        $data['link'] = [
            'title' => $title,
            'text' => $content,
            'picUrl' => $pic_url,
            'messageUrl' => $message_url
        ];
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);                
        return $data;  
    }

    public static function sendMarkdown($url, $title, $content, $at=[])
    {
        $data['msgtype'] = 'markdown';
        $data['markdown'] = [
            'title' => $title,
            'text' => $content,
        ];
        if (empty($at)) {
            $data['at'] = ['isAtAll' => true];
        } else {
            $data['at'] = ['atMobiles' => $at];
        }
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);                
        return $data;  
    }
}