<?php

namespace Air\Package\Wechat;

class WXAUtils {

    /**
     * @var WXUtil
     */
    private $wx_utils;

    public function __construct(WXUtil $wx_utils) {
        $this->wx_utils = $wx_utils;
    }


    /**
     * 获取 URL Link
     * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/qrcode-link/url-link/generateUrlLink.html
     * @param string $path
     * @param array $query
     * @param string $env_version release trial develop
     * @return array|bool
     */
    public function generate_urllink(string $path, array $query, string $env_version = 'release') {
        $url = 'https://api.weixin.qq.com/wxa/generate_urllink?access_token=%s';
        $access_token = $this->wx_utils->getBaseAccessToken();
        $url = sprintf($url, $access_token);
        $data = [
            'path' => $path,
            'query' => http_build_query($query)
        ];
        return $this->wx_utils::curl($url, $data, 1, 1, 'https');
    }

    /**
     * 获取 URL Schema
     * https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/qrcode-link/url-scheme/generateScheme.html
     * @param string $path
     * @param array $query
     * @param string $env_version release trial develop
     * @return array|bool
     */
    public function generate_scheme(string $path, array $query, string $env_version = 'release') {
        $url = 'https://api.weixin.qq.com/wxa/generatescheme?access_token=%s';
        $access_token = $this->wx_utils->getBaseAccessToken();
        $url = sprintf($url, $access_token);
        $data = [
            'jump_wxa' => [
                'path' => $path,
                'query' => http_build_query($query),
                'env_version' => $env_version
            ]
        ];
        return $this->wx_utils::curl($url, $data, 1, 1, 'https');

    }

}