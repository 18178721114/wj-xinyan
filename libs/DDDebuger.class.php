<?php
namespace Air\Libs;

use Air\Libs\Base\Utilities;
use Air\Package\Session\Helper\RedisSession;
use Phplib\Tools\Logger;

class DDDebuger {

    private $massage = [];
    private $api = NULL;
    private $request = NULL;
    private $user_name = NULL;

    public static function getDDDebuger() {
        static $singleton = NULL;
        is_null($singleton) && $singleton = new DDDebuger();
        return $singleton;
    }

    private function __construct() {
        $this->request = \Air\Libs\Base\HttpRequest::getAirRequest();
        $this->api = $this->request->uri;
        $uuid = $this->request->COOKIE[SESSION_ID];
        $userSession = RedisSession::getSession($uuid);
        $this->user_name = $userSession['name'];
    }
    public function setAPI($value) {
        $this->api = $value;
    }
    public function setUserName($value) {
        $this->user_name = $value;
    }

    public function clearMassage() {
        $this->massage = [];
    }

    
    public function addMassage($massage, $check_id = 'globe') {
        Logger::info(['check_id' => $check_id, 'data' => $massage], $massage[0]);
        $this->massage[$check_id][] = $massage;
    }

    public function sendMassage() {
        if ($this->massage) {
            $result = ['Debuger' => ["api" => $this->api, "user_name" => $this->user_name], "Massage" => $this->massage];
            Utilities::DDMonitor(json_encode($result, JSON_UNESCAPED_UNICODE), 'debuger');
            $this->clearMassage();
        }
    }
}
