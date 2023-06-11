<?php
namespace Air\Package\Wechat;
use Air\Package\Wechat\Helper\DBWechatTagHelper;

class WechatTag{

    public function addTag($tag) {
        $data = DBWechatTagHelper::getLines(['tag_id' => $tag['id'], 'tag_name' => $tag['name']]);
        if (empty($data)) {
            DBWechatTagHelper::create(['tag_id' => $tag['id'], 'tag_name' => $tag['name']]);
        }
    }

    public function getByTagName($tagName) {
        return DBWechatTagHelper::getLines(['tag_name' => $tagName]);
    }
}
