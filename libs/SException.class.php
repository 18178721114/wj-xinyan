<?php
namespace Air\Libs;

/**
 * params will be writen to a log automitically
 * @message: custom error words
 * @error_code: the custom code
 * @http_code: the http code
 * @related_args: other args for debug
 */
class SException extends \Exception {

    private $log = NULL;

    /**
     * http_code should in array(200, 400, 401, 404)
     */
    private $http_code = 400;
    private $related_args = array();

    public function __construct($message, $error_code = 11011, $http_code = 400, $related_args = array()) {
        $this->http_code = $http_code;
        $this->related_args = $related_args;

        parent::__construct($message, $error_code);
        $this->record();
    }

    public function getHttpCode() {
        return $this->http_code;
    }

    private function record() {
        $error_message = $this->getEMessage();
        \Phplib\Tools\Logger::error($this->getHttpCode() . " $error_message " . $this->getTraceAsString(), 'air_exception');
    }

    public function getEMessage() {
        $message = $this->getMessage();
        $file = $this->getFile();
        $line = $this->getLine();
        $code = $this->getCode();

        $error_message = "\"{$message}\" thrown by " . $file . " in line " . $line . " with code: " . $code;
        return $error_message;
    }

}
