<?php
/**
 * 发邮件
 */

namespace Air\Libs;


class Email
{
    public $email=null;
    private $sender = null;

    public function __construct($content_type = '')
    {
        $senders = explode(',', EMAIL_USER_NAME);
        $idx = rand(0, count($senders) - 1);
        $this->sender = $senders[$idx];
        $this->content_type = $content_type;
        $transport = (new \Swift_SmtpTransport(EMAIL_HOST, 587, 'TLS'))
            ->setPassword(\Phplib\MailKey::MAIL_PASSWORD)
            ->setTimeout(60)
            ->setUsername($this->sender);

        $this->email = new \Swift_Mailer($transport);
    }

    /**
     * @param $subject 标题
     * @param $to 接收人
     * @param $msg 内容
     * @param mixed $attach 附件
     * @param mixed $content_type 
     * @return int
     */
    public function send($subject, $to, $msg, $attach='', $content_type='', $cc = [], $bcc = [])
    {
        $message = (new \Swift_Message())
            ->setFrom([$this->sender => 'Airdoc系统邮件'])
            ->setSubject($subject)
            ->setTo($to)
            ->setBody($msg, $this->content_type);
        if (!empty($attach)) {
            if (is_array($attach)) {
                $attach_num = count($attach);
                if (!is_array($content_type)) {
                    $content_type = array_fill(0, $attach_num, $content_type);
                } else {
                    $content_type_num = count($content_type);
                    if ($attach_num != $content_type_num && $content_type_num < $attach_num) {
                        for ($i = $content_type_num; $i < $attach_num; $i++) {
                            $content_type[] = $content_type[$content_type_num - 1];
                        }
                    }
                }
                for ($i = 0; $i < $attach_num; $i++) {
                    $attachment = \Swift_Attachment::fromPath($attach[$i], $content_type[$i]);
                    $message->attach($attachment);
                }
            } else {
                $attachment = \Swift_Attachment::fromPath($attach, $content_type);
                $message->attach($attachment);
            }
        }
        if ($cc) {
            $message->setCc($cc);
        }
        if ($bcc) {
            $message->setBcc($bcc);
        }
        return $this->email->send($message);
    }
}
