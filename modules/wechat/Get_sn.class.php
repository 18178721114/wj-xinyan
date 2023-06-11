<?php

namespace Air\Modules\Wechat;


use Air\Package\Wechat\WXUtil;

class Get_sn extends \Air\Libs\Controller
{   
    // 通过微信二维码 获取sn
    public function run()
    {
        $request = $this->request->REQUEST;
        if(!$request['content']){
            $this->setView(1000, '请填写参数', '');
            return FALSE;

        }
        $data['raw_text'] = $request['content'];
        $ret = \Phplib\Tools\CommonFun::callOpenAPI(FD16_ADMIN_URl . '/api/fd16/getSnByRawText',$data);
        if($ret['error_code'] !=0 || !$ret['data']){
            $this->setView(1001, '未匹配到sn', '');
            return FALSE;
        }
        $this->setView(0, '获取成功', ['sn'=>$ret['data']]);
        return true;
    }
}
