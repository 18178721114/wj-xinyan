<?php

namespace Air\Libs;

use \Phplib\Tools\Logger;
use \Air\Package\Session\Helper\RedisSession;
use Air\Libs\Xcrypt;
use Air\Package\Cache\RedisCache;
use Air\Package\Oauth\AccessToken;

abstract class Controller
{
    protected $mode = 'json';
    protected $request = NULL;
    protected $userSession = NULL;
    protected $head = 200;
    protected $view = "";
    protected $runtime = 0;
    protected $error_code = 0;
    protected $message = 'OK';
    protected $client = NULL;
    protected $mClient = NULL;
    protected $nosession = FALSE;
    protected $must_login = FALSE;
    protected $error_code_prefix = 0;
    protected $must_security = FALSE;
    //jsonp callback
    protected $jsonpCallback = NULL;
    protected $token = '';
    protected $detailName = "/checklist/detail";
    protected $openAccessThrogh = false;

    /**
     * Subclasses must implement this method to route requests.
     */
    abstract public function run();

    //商业
    public function action_before()
    {
        if (ENV == 'test' && strpos('pre' . $this->request->domain, 'admin')) {
            $white_ips = [
                '116.247.81.186', '119.57.120.121', '127.0.0.1',
                '10.100.2.176', // wj办公室
                '36.112.64.2', // 北理工
                '172.31.0.1', // dev docker宿主机
                '39.107.84.77', // bms测试服务器
                '223.70.137.131', // 中电

            ];
            if (!in_array($this->request->ip, $white_ips)) {
                throw new \Air\Libs\SException('没有权限！', '100099');
                return FALSE;
            }
        }
        $this->error_code_prefix = \Air\Libs\ErrorCode::prefix($this->request->path);
        if (empty($this->token)) {
            $this->token = $this->request->COOKIE[SESSION_ID];
        }
        if (isset($this->must_login) && !empty($this->must_login)) {
            $this->userId = $this->currentUserId();
            if (!$this->userId && !$this->openAccessThrogh) {
                throw new \Air\Libs\SException('请先登录！', '100099');
            }
            //几层医生系统必须登录带机构id
            if (SESSION_ID == 'fantastic') {
                if (empty($this->userSession['org_id'])) {
                    throw new \Air\Libs\SException('请先登录！', '100099');
                }
            }
        }
    }

    // 鉴权
    public function security()
    {
        //判断是否有接口权限
        if (isset($this->must_security) && !empty($this->must_security)) {
            $authority = 'authority_pangu_' . $this->userSession['phone'];
            $info = RedisCache::getCache($authority);
            $uri = explode('?', $_SERVER['REQUEST_URI']);
            if (!in_array(trim($uri[0], '/'), json_decode($info))) {
                throw new \Air\Libs\SException('抱歉！您没有权限', '100090');
            }
        }
    }

    public function __construct(\Air\Libs\Base\HttpRequest $request, $userSession, $mode)
    {
        $this->request = $request;
        $this->mode = $mode;
    }

    public function InitializeSession($userSession, $uuid)
    {
        if (strpos('pre' . $this->request->domain, 'open')
            && !stripos('pre' . $this->request->uri, '/api/openapi/AccessToken')) {
            $userSession = $this->openAuth($uuid);
        }
        if ($userSession) {
            $this->userSession = $userSession;
        }
        $this->token = $uuid;
    }

    public function openAuth($uuid)
    {
        //判断是否access_token请求
        $headers = $this->request->headers;
        $accessToken = array_reverse(explode(AccessToken::HEADER_AUTHORIZATION_PARSE_PREFIX, $headers['Authorization'], 2))[0];
        if ($accessToken && strlen($accessToken) == AccessToken::ACCESS_TOKEN_LENGTH) {
            $response = AccessToken::parse($accessToken);
            if ($response['errNo'] === 0) {
                $this->userSession['org_id'] = $response['org']['id'];
                $this->openAccessThrogh = true;
                return;
            } elseif ($response['errNo'] !== 100) {
                $this->setError(400, 100401, 'Authorization is illegal!');
                return false;
            }
        }
        
        $appid = (int) $this->request->REQUEST['appid'];
        if (!$appid) {
            $this->setError(400, 100401, 'appid param is illegal!');
            return FALSE;
        }
        $sign = trim($this->request->REQUEST['sign']);
        $salt = trim($this->request->REQUEST['salt']);
        $uid = intval($this->request->REQUEST['user_id']);
        $t = intval($this->request->REQUEST['t']);
        if ($uid < 0) {
            $this->setError(400, 100401, 'user_id is illegal!');
            return FALSE;
        }
        $orgObj = new \Air\Package\User\Organizer();
        $uObj = new \Air\Package\User\User();
        $org = $orgObj->getOrganizerByAppid($appid);
        if (empty($org)) {
            $this->setError(400, 100401, 'appid is not exist!');
            return FALSE;
        }
        $org_id = $org['id'];
        $sign = strtolower($sign);
        $sign_eye = strtolower($sign);
        if (md5($appid . $salt . $org['secret_key'] . date('Y-m-d', $_SERVER['REQUEST_TIME'])) == $sign_eye) {
            //Utilities::DDMonitor("P3-pangu-签名方式用天的账号ID: $appid " . $_SERVER['REQUEST_URI'], 'cloudm');
            // $this->setError(400, 100409, 'sign method is wrong!');
            // return FALSE;
        }
        if (md5($appid . $salt . $org['secret_key'] . $t) == $sign || md5($appid . $salt . $org['secret_key'] . date('Y-m-d', $_SERVER['REQUEST_TIME'])) == $sign_eye || ENV == 'test' && $sign == 'asdfghjkl' . date('dm', time() - 86400)) {
            $userSession = RedisSession::getSession($uuid);
            if ($userSession['user_id'] > 0 && ($userSession['user_id'] == $uid || !$uid)) {
                return $userSession;
            }
            $uids = $uObj->getUserIds($org_id);
            if (empty($uids)) {
                $this->setError(400, 100405, 'account lost!');
                return FALSE;
            }
            if ($uid) {
                $user = $uObj->getUserById($uid);
                if (!$user || $user['org_id'] != $org_id || !$user['status']) {
                    $this->setError(400, 100402, 'user_id is illegal!');
                    return FALSE;
                }
            } else {
                $uids = $uObj->getUserIds($org_id);
                if (empty($uids)) {
                    $this->setError(400, 100402, 'sign is illegal!');
                    return FALSE;
                }
                $uid = $uids[0];
                $user = $uObj->getUserById($uid);
            }
            if (!in_array($user['type'], [1, 2])) {
                $this->setError(400, 100401, 'appid is illegal!');
                return FALSE;
            }
            //uuid is sign
            \Air\Package\Session\Helper\RedisSession::setSession($uuid, $user);
            return $user;
        } else {
            $this->setError(400, 100402, 'sign is illegal!');
            return FALSE;
        }
    }

    public function control()
    {
        try {
            $this->action_before();
            $this->security();
            $this->run();
        } catch (\Exception $e) {
            if (!$e instanceof \Air\Libs\SException) {
                Logger::info($e, 'control_exception');
                $this->setError(400, 400, $e->getMessage());
            } else {
                $http_code = $e->getHttpCode();
                $error_code = $e->getCode();
                $message = $e->getMessage();
                $this->setError($http_code, $error_code, $message);
            }
        }
    }

    public function checkStatusValid()
    {
        if (200 == $this->head) {
            return TRUE;
        }
        return FALSE;
    }

    public function getView()
    {
        return $this->view;
    }

    public function echoView()
    {
        $this->echoHeader();
        if ($this->mode == 'ht' || $this->mode == 'tx') {
            $func = 'formatJson';
        } else {
            $func = 'format' . ucwords($this->mode);
        }
        $output = $this->$func();

        //添加一个jsonp的输出
        if (!empty($this->jsonpCallback)) {
            $output = $this->jsonpCallback . "(" . $output . ")";
        }
        //输入输出日志，仅出错时记录 yhpay
        if (
            $this->runtime ||
            strpos('pre' . $this->request->path, 'yhpay') ||
            strpos('pre' . $this->request->path, 'vcode') ||
            strpos('pre' . $this->request->domain, 'open') ||
            strpos($this->request->path, 'signature') ||
            ($this->view && $this->view['error_code'] && $this->view['error_code'] != '122334')
        ) {
            $extra = [];
            if ($this->request->REQUEST['check_id']) {
                $extra['check_id'] = trim($this->request->REQUEST['check_id']);
            }
            $view = $this->view;
            if (empty($view) && !empty($this->error_code)) { //非200返回情况下，记录错误信息
                $view = [
                    'error_code' => $this->error_code,
                    'message' => $this->message,
                ];
            }
            Logger::info([$view, $this->request->REQUEST], 'runtime', $extra);
        }
        echo $output;
    }

    public function getJsonData()
    {
        return $this->formatJson();
    }

    private function formatJson()
    {
        if (200 === $this->head) {
            if (!empty($this->error_code) && empty($this->view)) {
                $this->view = [
                    'error_code' => $this->error_code,
                    'message' => $this->message,
                ];
            }
            $response = $this->view;
        } else {
            $response = array(
                'error_code' => $this->error_code,
                'message' => $this->message,
            );
        }
        return self::jsonize($response);
    }

    /**
     * 格式化数据
     * 此方法增加符合规则的controller加密，扩展性比较差，后续抽象出单独负责加密的cryptController，继承基础controller重写setView方法
     * 需要加密的业务controller调用cryptController来实现数据加密功能
     */
    protected function setView($errorCode = 0, $message = '请求成功', $data = NULL)
    {
        //根据URI判断是否是checklist下的detail相关controller
        if (ENV != 'production') {
            $this->view = array(
                'error_code' => (int) $errorCode,
                'message' => $message,
                'data' => $data,
            );
            return;
        }
        $unEncrypt = true;
        if (stripos($this->request->uri, $this->detailName) !== false) {
            // TODO $this->request->GET['crypt'] == 1 删掉
            if ($this->request->REQUEST['crypt'] != 'uncrypt_' . date('md', strtotime('last month')) && $data && $errorCode == 0) {
                $data_str = Xcrypt::encryptAes(json_encode($data), LOGIN_SK);
                // 加密过程混淆，把有用的第3、4位挪到倒数第4、3位以后，第3、4位用随机字符串补充。加密混淆后的字符串比原始加密串多两个字符。
                $data_str = substr($data_str, 0, 2) . substr(md5(time()), 2, 2) . substr($data_str, 4, -2) . substr($data_str, 2, 2) . substr($data_str, -2);
                $data  = $data_str;
                $message = 'crypt';
            }
        }
        $this->view = array(
            'error_code' => (int) $errorCode,
            'message' => $message,
            'data' => $data,
        );
    }

    protected function setError($head = 200, $errorCode = 0, $message = 'OK')
    {
        $this->head = $head;
        $this->error_code = $errorCode;
        $this->message = $message;
    }

    protected function echoHeader()
    {
        if (200 == $this->head) {
            switch ($this->mode) {
                case 'json':
                    header('Content-Type: application/json; charset=UTF-8');
                    break;
                case 'captcha':
                    ob_clean();
                    header('Content-type: image/jpeg;');
                    header("Cache-Control: no-cache");
                    header("Expires: -1");
                    break;
                default:
                    header('Content-Type: text/plain; charset=UTF-8');
            }
            return;
        }
        $this->setHeaderByHttpStatusCode($this->head);
    }

    protected function setHeaderByHttpStatusCode($code)
    {
        $codes = array(
            '400' => '400 Bad Request',
            '401' => '401 Unauthorized',
            '404' => '404 Not Found',
            '200' => '200 OK',
        );

        if (!isset($codes[$code])) {
            throw new \Exception(sprintf("Unknown HTTP status code: %s.", $code));
        }
        $code == 400 && $code = 200;

        header("HTTP/1.1 {$codes[$code]}");
    }

    public function currentUserId()
    {
        $user_id = (int) $this->userSession['user_id'];
        if (!empty($user_id)) {
            return $user_id;
        }
        return 0;
    }

    static protected function jsonize($data)
    {
        return \Air\Libs\Base\Utilities::jsonEncode($data, false, JSON_UNESCAPED_UNICODE);
    }

    public function asyncJob()
    {
    }
}
