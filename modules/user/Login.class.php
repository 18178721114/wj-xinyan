<?php

namespace Air\Modules\User;


use \Phplib\Tools\Logger;

class Login  extends \Air\Libs\Controller
{

    public function run()
    {
        $request = $this->request->REQUEST['code'];
    }
}
