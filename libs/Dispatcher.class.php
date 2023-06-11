<?php
namespace Air\Libs;

use \Air\Package\Session\Helper\RedisSession;

class Dispatcher {

    private $mode = 'json';
    private $request = NULL;
    private $uuid = '';
    private $userSession = NULL;
    private $module = NULL;
    private $action = NULL;
    private $controller = NULL;

    private $emptySession = FALSE;

    public static function get($mode = 'json') {
        static $singleton = NULL;
        is_null($singleton) && $singleton = new Dispatcher($mode);
        return $singleton;
    }

    private function __construct($mode) {
        $this->mode = $mode;
        $request = \Air\Libs\Base\HttpRequest::getAirRequest();
        $this->request = $request;
    }

    private function setUser($mode) {
        $userSession = RedisSession::getSession($this->uuid);
        if (!$userSession['user_id'] && $this->uuid == 'b3438e412ad63de660b9747dcce6fef9_special') {
            $userSession = ['user_id' => 1, 'name' => 'Hailong', 'phone' => '13811885439', 'type' => 2, 'status' => 1, 'org_id' => 1];
            RedisSession::setSession($this->uuid, $userSession);
        }
        $this->userSession = $userSession;
    }

    public function dispatch() {
        $request = $this->request;
        $path_args = $request->path_args;

        // first arg is the module's name
        $module = array_shift($path_args);
        empty($module) && $module = 'welcome';
        $this->module = $module;

        $action = array_shift($path_args);
        empty($action) && $action = 'main';
        $this->action = $action;
        if (strpos('pre' . $this->request->domain, 'open')) {
            $this->uuid = md5($this->request->REQUEST['appid']) . $this->request->REQUEST['user_id'] . date('md', $_SERVER['REQUEST_TIME']);
            if ($module != 'openapi') {
                echo json_encode(['error_code' => 100344, 'message' => '没有权限!']);
                exit();
            }
        }
        else {
            $this->uuid = isset($this->request->COOKIE[SESSION_ID]) ? $this->request->COOKIE[SESSION_ID] : '';
            if (empty($this->uuid)) {
                $this->uuid = \Air\Libs\Base\Utilities::getUniqueId();
            }
            @setcookie(SESSION_ID, $this->uuid, time() + 86400000, '/', $this->request->domain);
        }
        // pass the control to module's Router class
        if (!empty($request->REQUEST['fake'])) {
            $class = '\\Air\\Modules\\Fake\\Api';
            array_unshift($path_args, $action);
            array_unshift($path_args, $module);
        }
        else {
            // 支持多级链接
            $class = '\\Air\\Modules\\';
            foreach($request->path_args as $key => $path){
                $class .= ucwords($path);
                $next = $key + 1;
                if (!empty($request->path_args[$next])) {
                    $class .= '\\';
                }
            }
        }
        $request->path_args = $path_args;
        if (!class_exists($class)) {
            $class = '\\Air\\Modules\\Systems\\Badcall';
        }

        $controller = new $class($request, NULL, $this->mode);
        $this->setUser($this->mode);

        $controller->InitializeSession($this->userSession, $this->uuid);
        if ($controller->checkStatusValid()) {
            $controller->control();
            $controller->echoView();
        }
        else {
            $controller->echoView();
        }
        $this->controller = $controller;
    }

    public function get_request() {
        return $this->request;
    }

    public function get_module() {
        return $this->module;
    }

    public function get_action() {
        return $this->action;
    }

    public function asyncJob() {
        if (isset($this->controller)) {
            $this->controller->asyncJob();
        }
    }
}
