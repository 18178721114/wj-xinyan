<?php
namespace Air\Libs;

use \Air\Package\Session\Helper\RedisSession;
use \Air\Package\User\Helper\RedisAntispam;
use \Air\Libs\Xcrypt;
trait InternalCall
{
    private $secret_keys = [
        'default' => INTERNAL_SECRET,
        'check_log_add' => INTERNAL_SECRET, //检查单生命周期内部调用key
    ];

    public function verifySign($from = 'default') {
        foreach ($this->request->REQUEST as $key => &$val) {
            $val = trim($val);
        }
        if (empty($this->request->REQUEST['sign'])) {
            $this->setView(10001, '缺少Sign');
            return FALSE;
        }
        if (empty($this->request->REQUEST['t'])) {
            $this->setView(10002, '缺少t');
            return FALSE;
        }
        $req = $this->request->REQUEST;
        if ($req['t'] < time() - 36000 || $req['t'] > time() + 36000) {
            $this->setView(10002, 't参数失效');
            return FALSE;
        }
        if (!in_array($from, array_keys($this->secret_keys))) {
            $this->setView(10002, 'from参数失效');
            return FALSE;
        }
        $secret_key = $this->secret_keys[$from];
        $sign = md5(sha1($secret_key . $req['t'] . $req['salt']));
        if ($sign != $req['sign']) {
            $this->setView(999999, '签名验证失败');
            return FALSE;
        }
        return true;
    }

    public function verifySignDeviceInitialize($devsn) {

        $req = $this->request->REQUEST;
        if (empty($req['sign'])) {
            $this->setView(10001, '缺少Sign');
            return false;
        }

        if (empty($devsn)) {
            $this->setView(10001, '缺少Devsn');
            return false;
        }

        if (empty($req['adcsn'])) {
            $this->setView(10001, '缺少Adcsn');
            return false;
        }

        if (empty($req['t'])) {
            $this->setView(10001, '缺少t');
            return false;
        }

        if ($req['t'] < time() - 36000 || $req['t'] > time() + 36000) {
            $this->setView(10002, 't参数失效');
            return false;
        }
        
        $sign = md5($devsn . $req['adcsn'] . $req['salt'] . $req['t']);
        if ($sign != $req['sign']) {
            $this->setView(999999, '签名验证失败');
            return false;
        }
        return true;
    }

    public function verifySignFd16InitializeV3($sn, $devsecret) {

        $req = $this->request->REQUEST;
        if (!$sn) {
            $this->setView(10001, '缺少sn');
            return false;
        }

        if (empty($req['sign'])) {
            $this->setView(10001, '缺少Sign');
            return false;
        }

        if (empty($req['t'])) {
            $this->setView(10001, '缺少t');
            return false;
        }

        if ($req['t'] < time() - 300 || $req['t'] > time() + 300) {
            $this->setView(10002, 't参数失效');
            return false;
        }
        
        $source = $sn . $req['salt'] . $req['t'];
        $sign = Xcrypt::encryptFd16($source, $devsecret);

        if ($req['sign'] != $sign) {
            $this->setView(999999, '签名验证失败');
            return false;
        }
        return true;
    }
}
