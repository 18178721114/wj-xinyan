<?php

namespace Air\Libs\Srv;

use Air\Libs\Base\HttpRequest;
use Air\Libs\SException;
use Air\Package\App\App;
use Phplib\Tools\Logger;

abstract class Controller extends \Air\Libs\Controller
{

    public $appid = 0;
    public $app_info = [];
    public $service_name = '';
    public $method_name = '';
    public $must_login = FALSE;

    public function __construct(HttpRequest $request, $user_session, $mode)
    {
        // if (ENV == 'test') {
        //     $this->runtime = 1;
        // }
        
        parent::__construct($request, $user_session, $mode);
    }

    public function action_before()
    {
        parent::action_before();

        $this->getServiceInfo();
        $this->authenticate();
    }

    private function authenticate()
    {
        $request = $this->request->REQUEST;
        $this->appid = $request['appid'];
        $t = $request['t'];
        $app_info = $this->getAppInfo($this->appid);
        if (!$app_info) {
            throw new SException('appid is error', '100099');
        }
        $this->app_info = $app_info;
        $current_time = time();
        if ($t < $current_time - 3600 || $t > $current_time + 3600) {
            throw new SException('t is error', '100099');
        }
        $sign = sha1(sha1($this->appid . $app_info['secret_key']) . $t);
        if ($sign != $request['sign']) {
            throw new SException('sign is error', '100099');
        }
        if (!$this->isPermission()) {
            throw new SException('Permission is not enough', '100099');
        }
    }

    public function formatParameter($param, $type)
    {
        if (strpos($param, ',')) {
            $param = explode(',', $param);
            $new_param = [];
            foreach ($param as $item) {
                if ($type == 'int') {
                    if (!is_numeric($item)) {
                        continue;
                    }
                    $new_param[] = $item;
                } else {
                    $new_param[] = trim($item);
                }
            }
            $param = $new_param;
        } else {
            if ($type == 'int') {
                if (!is_numeric($param)) {
                    $param = 0;
                }
            } else {
                $param = trim($param);
            }
        }

        return $param;
    }

    //获取当前请求的service_name,method_name
    private function getServiceInfo()
    {
        $path = $this->request->path;
        list(, $service_name, $method_name) = explode('/', $path);
        if (!$service_name || !$method_name) {
            throw new SException('request path is error', '100099');
        }
        $this->service_name = $service_name;
        $this->method_name = $method_name;
    }

    //判读当前应用是否有权限访问此服务接口
    private function isPermission()
    {
        $permission_list = $this->app_info['permission_list'];
        if (isset($permission_list[$this->service_name][$this->method_name])) {
            return TRUE;
        }

        return FALSE;
    }

    private function getAppInfo($appid)
    {
        $app = new App();
        $info = $app->getAppInfo($appid);
        if (!$info) {
            return FALSE;
        }
        return $info;
    }

    //监控用户相关规则
    public function monitorUser($users)
    {
        $permission = $this->app_info['permission_list'][$this->service_name][$this->method_name];
        $system_id_flag = 0;
        foreach ($users as $k => $user) {
            if (isset($permission['rules']['system_id']) && $permission['rules']['system_id']) {
                if ($user['system_id'] != $permission['rules']['system_id']) {
                    $system_id_flag++;
                }
            }
        }
        if ($system_id_flag > 0) {
            Logger::error(['system_id_flag' => $system_id_flag], 'srv_error');
        }
    }

    //监控机构相关规则
    public function monitorOrganizer($orgs)
    {
        $permission = $this->app_info['permission_list'][$this->service_name][$this->method_name];
        $system_id_flag = 0;
        foreach ($orgs as $k => $org) {
            if (isset($permission['rules']['system_id']) && $permission['rules']['system_id']) {
                if ($org['system_id'] != $permission['rules']['system_id']) {
                    $system_id_flag++;
                }
            }
        }
        if ($system_id_flag > 0) {
            Logger::error(['system_id_flag' => $system_id_flag], 'user_center');
        }
    }
}
