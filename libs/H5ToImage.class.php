<?php

namespace Air\Libs;

use Air\Package\Checklist\Image;
use \Air\Package\Checklist\QiniuHandler;

/**
 * h5页面转化为image图片
 * 仅在命令行执行时可调用
 * 保证执行的服务器环境安装了/bin/timeout /opt/phantomjs 以及/opt/longimage_png.js脚本
 */
class H5ToImage
{
    /**
     * h5转化为image
     * @param $h5_url h5链接地址
     * @param $file_path_name 指定转化后的图片本地存放路径及文件名，如果指定，需要调用方自己清理本地文件。如果不指定，函数自行指定本地文件地址，并自行清理
     * @return 返回转化后图片的url地址,转化失败返回false,并抛出异常
     */
    public static function convert($h5_url, $file_path_name = '')
    {
        if (!$h5_url || substr($h5_url, 0, 4) != 'http') {
            throw new \Exception('h5_url is error: ' . $h5_url, '-1');
            return FALSE;
        }
        $file_path = '';
        if (!$file_path_name) {
            $file_path = tempnam('/tmp', 'h5-to-image') . '.png';
        } else {
            $file_path = $file_path_name;
        }

        $path_info = pathinfo($file_path);
        $cmd = "/bin/timeout 120 /opt/phantomjs /opt/longimage_png.js '%s' %s %s";
        $cmd = sprintf($cmd, $h5_url, $path_info['dirname'], $path_info['basename']);

        //转化处理
        $result = '';
        system($cmd, $result);

        //文件不存在，转化失败
        if (!file_exists($file_path)) {
            \Phplib\Tools\Logger::error(['cmd' => $cmd, 'result' => $result], 'h5toimage');
            throw new \Exception('h5 convert to image is failed cmd: ' . $cmd, '-2');
            return FALSE;
        }

        //上传图片到存储并获取url地址
        $content = file_get_contents($file_path);
        $remote_filename = 'customer_image/h5toimage/' . date('Y-m-d') . '/' . $path_info['basename'];
        //zj $ret = QiniuHandler::uploadFile($file_path, $remote_filename, 'fundus');
        $ret = Image::uploadImage($file_path, $remote_filename);
        $url = '';
        if ($ret) {
            //zj $url = IMG_DOMAIN_NEW_HTTPS . $ret[0]['key'];
            $url = $ret;
        } else {
            if (!$file_path_name) {
                unlink($file_path);
            }
            \Phplib\Tools\Logger::error(['file_path' => $file_path, 'remote_filename' => $remote_filename, 'ret' => $ret], 'h5toimage');
            throw new \Exception('image upload is failed remote_filename: ' . $remote_filename, '-3');
            return FALSE;
        }

        if (!$file_path_name) {
            unlink($file_path);
        }

        return $url;
    }
}
