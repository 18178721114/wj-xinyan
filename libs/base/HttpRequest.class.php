<?php

namespace Air\Libs\Base;

use \Air\Libs\Email;
use Air\Libs\Xcrypt;
use \Air\Package\User\Helper\RedisMonitor;

class HttpRequest
{

    private $request_data = NULL;

    public static function getAirRequest()
    {
        static $singleton = NULL;
        is_null($singleton) && $singleton = new HttpRequest();
        return $singleton;
    }

    private function __construct()
    {
        $this->request_data['protocol']  = $_SERVER['SERVER_PROTOCOL'];
        $this->request_data['domain']    = $_SERVER['HTTP_HOST'];
        $this->request_data['uri']       = $_SERVER['REQUEST_URI'];
        $this->request_data['path']      = $this->getRequestPath();
        $this->request_data['path_args'] = explode('/', $this->path);
        if ($this->request_data['path_args'][0] == 'api') {
            array_shift($this->request_data['path_args']);
        }
        $this->request_data['method']    = $this->getRequestMethod();
        $this->request_data['GET']       = \Air\Libs\Base\Utilities::zaddslashes(\Air\Libs\Base\Utilities::unmark_amps($_GET));
        $this->request_data['POST']      = \Air\Libs\Base\Utilities::zaddslashes(\Air\Libs\Base\Utilities::unmark_amps($_POST));
        $this->request_data['COOKIE']    = \Air\Libs\Base\Utilities::zaddslashes($_COOKIE);
        $this->request_data['REQUEST']   = \Air\Libs\Base\Utilities::zaddslashes($_REQUEST);
        $this->request_data['headers']   = \Air\Libs\Base\Utilities::parseRequestHeaders();
        $this->request_data['requri']    = isset($this->request_data['headers']['Requrl']) ? $this->request_data['headers']['Requrl'] : "";
        $this->request_data['base_url']  = $this->detectBaseUrl();
        $this->request_data['agent']     = \Air\Libs\Base\Utilities::getBrowerAgent();
        $this->request_data['refer']     = isset($this->request_data['headers']['Referer']) ? $this->request_data['headers']['Referer'] : "";
        $this->request_data['ip']        = $this->getIP();
        $this->request_data['time']      = $_SERVER['REQUEST_TIME'];
        $this->renderRequest();
    }

    private function renderRequest()
    {
        if (isset($this->request_data['headers']['Content-Type']) && stripos('pre' . $this->request_data['headers']['Content-Type'], 'application/json')) {
            $json_origin = $json = file_get_contents("php://input");

            if ($this->request_data['path'] == 'api/openapi/receive_base_info_zhijian') {
                $json = Xcrypt::decryption_rsa(PRIVATE_KEY, $json);
            }
            if ($json) {
                $json = mb_convert_encoding($json, "UTF-8", "UTF-8");
                $json = str_replace(array("\r\n", "\t", "\r", "\n"), "", $json);
                if (strpos($json, '1381908280001')) {
                    echo json_encode(['error_code' => 0]);
                    exit;
                }
                $array = json_decode($json, 1);
                if (empty($array)) {
                    $json = str_replace("\\\"", "", $json);
                    $json = str_replace("\\", "", $json);
                    $array = json_decode($json, 1);
                }
                if (empty($array)) {
                    \Phplib\Tools\Logger::info($json, 'json_text');
                    $md5 = strlen($json);
                    $check2 = RedisMonitor::getFlag('reported' . $md5);
                    if (empty($check2)) {
                        $email_obj = new Email();
                        $email_obj->send('【' . ENV . '】无法解析json for API ' . $_SERVER['REQUEST_URI'], ['zhangbing@airdoc.com', 'zhangshilong1214@airdoc.com', 'wengjing@airdoc.com'], $json_origin);
                        RedisMonitor::setFlag('reported' . $md5, 1, 3600);
                    }
                }
                $this->request_data['REQUEST'] = $array;
            }
        }
    }

    public function &__get($name)
    {
        if (!isset($this->request_data[$name])) {
            return 0;
        }
        return $this->request_data[$name];
    }


    /**
     * Returns the requested URL path.
     */
    private function getRequestPath()
    {
        // only parse $path once in a request lifetime
        static $path;

        if (isset($path)) {
            return $path;
        }

        if (isset($_SERVER['REQUEST_URI'])) {
            // extract the path from REQUEST_URI
            $request_path = strtok($_SERVER['REQUEST_URI'], '?');
            $base_path_len = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));

            // unescape and strip $base_path prefix, leaving $path without a leading slash
            $path = substr(urldecode($request_path), $base_path_len + 1);

            // $request_path is "/" on root page and $path is FALSE in this case
            if ($path === FALSE) {
                $path = '';
            }

            // if the path equals the script filename, either because 'index.php' was
            // explicitly provided in the URL, or because the server added it to
            // $_SERVER['REQUEST_URI'] even when it wasn't provided in the URL (some
            // versions of Microsoft IIS do this), the front page should be served
            if ($path == basename($_SERVER['PHP_SELF'])) {
                $path = '';
            }
        }

        return $path;
    }

    private function getRequestMethod()
    {
        static $method;

        if (isset($method)) {
            return $method;
        }

        $method = strtolower($_SERVER['REQUEST_METHOD']);
        // make sure $method is valid and supported
        in_array($method, array('get', 'post', 'delete')) || $method = 'get';

        return $method;
    }

    private function detectBaseUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $host = $_SERVER['SERVER_NAME'];
        $port = ($_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT']);
        $uri = preg_replace("/\?.*/", '', $_SERVER['REQUEST_URI']);

        return "$protocol$host$port";
    }

    private function getIP()
    {
        static $ip;

        if (isset($ip)) {
            return $ip;
        }

        if (empty($this->request_data['headers']['Clientip'])) {
            $ip = "127.0.0.1";
        } elseif (!strpos($this->request_data['headers']['Clientip'], ",")) {
            $ip = $this->request_data['headers']['Clientip'];
        } else {
            $hosts = explode(',', $this->request_data['headers']['Clientip']);
            foreach ($hosts as $host) {
                $host = trim($host);
                if ($host != "unknown") {
                    $ip = $host;
                    break;
                }
            }
        }

        return $ip;
    }

    public function getRequestArgs()
    {
        return array(
            'HEADERS' => $this->request_data['headers'],
            'POST' => $this->request_data['POST'],
            'GET' => $this->request_data['GET']
        );
    }
}
