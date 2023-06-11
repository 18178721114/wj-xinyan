<?php

/**
 * @ Purpose:
 * 提供给平安回调的controller
 */

namespace Air\Libs;

class PinganCallbackController extends Controller
{

    public function __construct()
    {
    }

    public function run()
    {
    }

    protected function setView($errorCode = 0, $message = '请求成功', $data = NULL)
    {
        $this->view = array(
            'returnCode' =>  $errorCode,
            'returnMsg' => $message,
        );
    }
}
