<?php

namespace Air\Package\Wechat;

class WechatMsgTemplate {
    const MSG_COMMON_PICTEXT = <<<HEREDOC
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <ArticleCount>1</ArticleCount>
  <Articles>
    <item>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <PicUrl><![CDATA[%s]]></PicUrl>
    <Url><![CDATA[%s]]></Url>
    </item>
   </Articles>
</xml>
HEREDOC;

    const MSG_CUSTOMER_SERVICE = <<<HEREDOC
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>
HEREDOC;

    const MSG_COMMON_TEXT = <<<HEREDOC
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA[%s]]></Content>
</xml>
HEREDOC;

    const MSG_COMMON_IMAGE = <<<HEREDOC
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[image]]></MsgType>
  <Image><MediaId><![CDATA[%s]]></MediaId></Image>
</xml>
HEREDOC;

   const MSG_WELCOME_STR = <<<HEREDOC
1. 点击“开始筛查”获取筛查二维码
2. 点击“查看报告”查看您的筛查报告
3. 公众号中发送消息可获得专业医师咨询服务

祝您身体健康！
HEREDOC;


    const MSG_OUT_OF_SERVICE_STR =  <<<HEREDOC
您好，欢迎使用Airdoc慧心瞳视网膜评估，我们的工作时间为周一至周五9:00-18:00，您现在可以进行留言，客服人员上班后会第一时间回复您。
HEREDOC;

    const MSG_TEMPLATE_REPLY = <<<HEREDOC
<xml>
  <ToUserName><![CDATA[%s]]></ToUserName>
  <FromUserName><![CDATA[%s]]></FromUserName>
  <CreateTime>%s</CreateTime>
  <MsgType><![CDATA[%s]]></MsgType>
  <Event><![CDATA[%s]]></Event>
  <MsgID>%s</MsgID>
  <Status><![CDATA[%s]]></Status>
</xml>
HEREDOC;
}
