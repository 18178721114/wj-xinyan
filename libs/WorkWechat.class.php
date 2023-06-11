<?php
/**
 * 企业微信机器人
 */
namespace Air\Libs;

use Air\Libs\Base\Utilities;

/**
 * https://work.weixin.qq.com/help?person_id=1&doc_id=13376
 */

class WorkWechat
{

    private $endpoint;

    const HTTP_OPTIONS = [
        'is_json' => 1,
        'is_post' => 1,
        'need_decode' => 1,
        'header' => [
            'Content-Type: application/json'
        ]
    ];

    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function sendText($content, $mentioned_list = ['@all'])
    {
        $body = [
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
                'mentioned_list' => $mentioned_list
            ]
        ];

        $result = $this->sendHttpRequest($body);

        return $result;
    }

    public function sendMarkdown($content, $mentioned_list = ['@all'])
    {
        $body = [
            'msgtype' => 'markdown',
            'markdown' => [
                'content' => $content,
                'mentioned_list' => $mentioned_list
            ]
        ];

        $result = $this->sendHttpRequest($body);

        return $result;
    }

    public function sendImage($base64_image, $md5, $mentioned_list = ['@all'])
    {
        $body = [
            'msgtype' => 'image',
            'image' => [
                'base64' => $base64_image,
                'md5' => $md5,
                'mentioned_list' => $mentioned_list
            ]
        ];

        $result = $this->sendHttpRequest($body);

        return $result;
    }

    public function sendNews($articles, $mentioned_list = ['@all'])
    {
        $body = [
            'msgtype' => 'news',
            'news' => [
                'articles' => $articles,
                'mentioned_list' => $mentioned_list
            ]
        ];

        $result = $this->sendHttpRequest($body);
    }

    private function sendHttpRequest($body)
    {
        $ret = Utilities::curl($this->endpoint, $body, self::HTTP_OPTIONS);

        return $ret;
    }
}
