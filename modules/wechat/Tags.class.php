<?php
/**
 * Date: 2017/10/13
 * Time: 下午4:57
 */

namespace Air\Modules\Wechat;


use Air\Package\Wechat\WXUtil;

class Tags extends \Air\Libs\Controller
{
    public function run()
    {
        $request = $this->request;
        if ($request->REQUEST['new']) {
            $obj = new WXUtil(WX_APPID_NEW, WX_SECRET_NEW);
        }
        else {
            $obj = new WXUtil(WX_APPID, WX_SECRET);
        }
        $result = $obj->createTags();
        $tags = $obj->getTags();
        $this->setView(0, '', [$result, $tags]);
    }
}
