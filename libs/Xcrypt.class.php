<?php

/**
 * 对称加密
 */

namespace Air\Libs;

use Air\Libs\Base\Utilities;
use Air\Package\Kms\KmsHandler;
use Phplib\Tools\Logger;

class Xcrypt
{
    /**
     * @desc 加密
     * @param $data
     * @return string
     */
    public static function encrypt($plaintext)
    {
        return KmsHandler::encrypt($plaintext);
        $key = '';
        $cipher = 'AES-128-ECB';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = $ivlen ? openssl_random_pseudo_bytes($ivlen) : false;
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }

    public static function decrypt($endata)
    {
        if (strlen($endata) < 90) {
            return self::decryptOld($endata);
        } else {
            $plaintext = KmsHandler::decrypt($endata);
            if ($plaintext) {
                return $plaintext;
            }
            return self::decryptOld($endata);
        }
    }

    private static function decryptOld($endata)
    {
        $key = '';
        $c = base64_decode($endata);
        $cipher = 'AES-128-ECB';
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        if ($original_plaintext) {
            if (is_numeric($original_plaintext) && $original_plaintext > MAX_CRYPT_CHECK_ID) {
                $debug = debug_backtrace();
                Logger::error(['string' => $endata, 'api' => 'decryptOld', 'original_plaintext' => $original_plaintext, 'debug' => $debug], "kms_exception");
                Utilities::DDMonitor("P3-pangu-检查号加密算法不符合规范，检查号：{$original_plaintext}", 'dev', TRUE);
            }
            return $original_plaintext;
        }
        return KmsHandler::decrypt($endata);
    }

    public static function encryptNew($plaintext)
    {
        $cipher = 'AES-128-ECB';
        $key = ENCRYPT_KEY;
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = $ivlen ? openssl_random_pseudo_bytes($ivlen) : false;
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);
        return $ciphertext;
    }

    public static function decryptNew($endata)
    {
        $c = base64_decode($endata);
        $cipher = 'AES-128-ECB';
        $key = ENCRYPT_KEY;
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, true);
        if (hash_equals($hmac, $calcmac)) //PHP 5.6+ timing attack safe comparison
        {
            return $original_plaintext;
        }
        return $original_plaintext;
    }

    /**
     *
     * @param string $string 需要加密的字符串
     * @param string $key 密钥
     * @return string
     */
    public static function encryptPA($string)
    {
        $key = ENCRYPT_KEY_PA;
        // 对接java，服务商做的AES加密通过SHA1PRNG算法（只要password一样，每次生成的数组都是一样的），Java的加密源码翻译php如下：
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);

        // openssl_encrypt 加密不同Mcrypt，对秘钥长度要求，超出16加密结果不变
        $data = openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = strtolower(bin2hex($data));

        return $data;
    }


    /**
     * @param string $string 需要解密的字符串
     * @param string $key 密钥
     * @return string
     */
    public static function decryptPA($string)
    {
        $string = strtolower($string);
        $key = ENCRYPT_KEY_PA;
        // 对接java，服务商做的AES加密通过SHA1PRNG算法（只要password一样，每次生成的数组都是一样的），Java的加密源码翻译php如下：
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $decrypted = openssl_decrypt(hex2bin($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $decrypted;
    }

    public static function mysql_aes_key($key)
    {
        $new_key = str_repeat(chr(0), 16);
        for ($i = 0, $len = strlen($key); $i < $len; $i++) {
            $new_key[$i % 16] = $new_key[$i % 16] ^ $key[$i];
        }
        return $new_key;
    }

    public static function aes_encrypt($val)
    {
        $key = self::mysql_aes_key(CRYPT_KEY);
        $pad_value = 16 - (strlen($val) % 16);
        $val = str_pad($val, (16 * (floor(strlen($val) / 16) + 1)), chr($pad_value));
        return base64_encode(openssl_encrypt($val, "aes-128-ecb", $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING));
    }

    public static function aes_decrypt($val)
    {
        $key = self::mysql_aes_key(CRYPT_KEY);
        $val = openssl_decrypt(base64_decode($val), "aes-128-ecb", $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
        $dec_s = strlen($val);
        $padding = ord($val[$dec_s - 1]);
        $val = substr($val, 0, -$padding);
        return @rtrim($val, "\0..\16");
    }
    //对java或其他的语言给的公钥和私钥格式化
    public static function format_secret_key($secret_key, $type)
    {
        // 64个英文字符后接换行符"\n",最后再接换行符"\n"
        $key = (wordwrap($secret_key, 64, "\n", true)) . "\n";
        // 添加pem格式头和尾
        if ($type == 'pub') {
            $pem_key = "-----BEGIN PUBLIC KEY-----\n" . $key . "-----END PUBLIC KEY-----\n";
        } else if ($type == 'pri') {
            $pem_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $key . "-----END RSA PRIVATE KEY-----\n";
        } else {
            echo ('公私钥类型非法');
            exit();
        }
        return $pem_key;
    }
    // 用rsa加密
    public static function encryption_rsa($pem, $data, $type)
    {
        $pem_info = self::format_secret_key($pem, $type);
        // $res = openssl_pkey_new();
        // openssl_pkey_export($res, $pri);
        $encrypted = '';
        if ($type == 'pub') {
            $pi_key =  openssl_pkey_get_public($pem_info);
            if (strlen($data) >= 117) {
                $status = false;
                foreach (str_split($data, 117) as $chunk) {
                    $status = openssl_public_encrypt($chunk, $encryptData, $pi_key);
                    $encrypted .= $encryptData;
                }
                return $status ? base64_encode($encrypted) : null;
            }
            return openssl_public_encrypt($data, $encrypted, $pi_key) ? base64_encode($encrypted) : null;
        } else if ($type == 'pri') {
            $pi_key =  openssl_pkey_get_private($pem_info);
            if (strlen($data) > 117) {
                $status = false;
                foreach (str_split($data, 117) as $chunk) {
                    $status = openssl_private_encrypt($chunk, $encryptData, $pi_key);
                    $encrypted .= $encryptData;
                }
                return $status ? base64_encode($encrypted) : null;
            }
            return openssl_private_encrypt($data, $encrypted, $pi_key) ? base64_encode($encrypted) : null;
        }
    }

    // 用rsa私钥解密
    public static function decryption_rsa($pem, $data, $type = 'pri')
    {
        $pem_info = self::format_secret_key($pem, $type);
        // $res = openssl_pkey_new();
        // openssl_pkey_export($res, $pri);
        $decrypted = '';
        $pi_key =  openssl_pkey_get_private($pem_info);
        if (strlen($data) >= 128) {
            $status = false;
            foreach (str_split(base64_decode($data), 128) as $chunk) {
                $status = openssl_private_decrypt($chunk, $decryptData, $pi_key);
                $decrypted .= $decryptData;
            }
            return $status ? $decrypted : null;
        }
        return openssl_private_decrypt($data, $encrypted, $pi_key) ? base64_decode($encrypted) : null;
    }

    //FD16加密协议
    public static function encryptFd16($plaintext, $devsecret)
    {
        $cipher = 'AES-128-ECB';
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $devsecret, OPENSSL_RAW_DATA);
        $ciphertext = base64_encode($ciphertext_raw);
        return $ciphertext;
    }

    public static function decryptFd16($endata, $devsecret)
    {
        $c = base64_decode($endata);
        $cipher = 'AES-128-ECB';
        $plaintext = openssl_decrypt($c, $cipher, $devsecret, OPENSSL_RAW_DATA);
        return $plaintext;
    }

    /**
     * @param string $string 需要加密的字符串
     * @return string $decrypted
     */
    public static function encryptAes($string, $key)
    {
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $data = openssl_encrypt($string, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = strtolower(bin2hex($data));
        return $data;
    }

    /**
     * @param string $string 需要解密的字符串
     * @return string $decrypted
     */
    public static function decryptAes($string, $key)
    {
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $decrypted = openssl_decrypt(hex2bin($string), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $decrypted;
    }

    /**
     * 标准加密
     * @param string $string 需要加密的字符串
     * @param string $key 密钥
     * @return string
     */
    public static function encryptPAStandard($string,  $key = '')
    {
        $iv = "1234567890123456";
        !$key && $key = ENCRYPT_KEY_PA;
        // 对接java，服务商做的AES加密通过SHA1PRNG算法（只要password一样，每次生成的数组都是一样的），Java的加密源码翻译php如下：
        //$key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $data = openssl_encrypt($string, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        $data = base64_encode($data);

        return $data;
    }


    /**
     * 标准解密
     * @param string $string 需要解密的字符串
     * @param string $key 密钥
     * @return string
     */
    public static function decryptPAStandard($string, $key = '')
    {
        $iv = "1234567890123456";
        !$key && $key = ENCRYPT_KEY_PA;
        // 对接java，服务商做的AES加密通过SHA1PRNG算法（只要password一样，每次生成的数组都是一样的），Java的加密源码翻译php如下：
        //$key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $decrypted = openssl_decrypt(base64_decode($string), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }
}
