<?php

namespace Air\Libs\Base;

use Air\Package\User\Sms;
use \Phplib\Tools\Logger;

class Utilities
{

    public static function parseRequestHeaders()
    {
        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        $headers['Clientip'] = getenv('REMOTE_ADDR');
        if (!empty(getenv('HTTP_X_FORWARDED_FOR'))) {
            $headers['Clientip'] = getenv('HTTP_X_FORWARDED_FOR');
        }
        if (!empty(getenv('HTTP_CLIENT_IP'))) {
            $headers['Clientip'] = getenv('HTTP_CLIENT_IP');
        }
        return $headers;
    }

    // 过滤掉emoji表情
    public static function filterEmoji($str)
    {
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str
        );

        return $str;
    }

    public static function convertStringToUtf8($string)
    {
        $string = urldecode($string);
        $encoding = mb_detect_encoding($string, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
        $string = mb_convert_encoding($string, 'UTF-8', $encoding);
        $string = trim(str_replace('GB2312', 'utf-8', $string)); //换xml类型编码
        return $string;
    }

    /**
     * Jsonize data and indents the flat JSON string to make it more
     * human-readable.
     *
     * @link
     * http://recursive-design.com/blog/2008/03/11/format-json-with-php/
     *
     * @param mixed $data The data to be jsonized.
     * @return string Indented version of the original JSON string.
     */
    public static function jsonEncode($data, $pretty_print = FALSE, $options = 0)
    {
        $json = json_encode($data, $options);
        if (json_last_error()) {
            json_last_error() == 5 && $json = json_encode(\Air\Libs\Base\Utilities::utf8_encode_mix($data));
        }
        if (!$pretty_print) {
            return $json;
        }

        $result = '';
        $pos = 0;
        $str_len = strlen($json);
        $indent_str = '    ';
        $new_line = "\n";
        $prev_char = '';
        $out_of_quotes = TRUE;

        for ($i = 0; $i <= $str_len; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string?
            if ($char == '"' && $prev_char != '\\') {
                $out_of_quotes = !$out_of_quotes;
            } else if (($char == '}' || $char == ']') && $out_of_quotes) {
                // If this character is the end of an element,
                // output a new line and indent the next line.
                $result .= $new_line;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indent_str;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $out_of_quotes) {
                $result .= $new_line;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indent_str;
                }
            }

            $prev_char = $char;
        }

        return $result;
    }


    //用header跳转
    public static function headerToUrl($destUrl, $extHeader = array())
    {
        $destUrl = htmlspecialchars_decode($destUrl);

        if (!empty($extHeader)) {
            foreach ($extHeader as $v) {
                header($v);
            }
        }
        header("Location: {$destUrl}");
    }

    public static function isSearchEngine($agentStr = '')
    {
        if (empty($agentStr)) {
            $agentStr = $_SERVER['HTTP_USER_AGENT'];
        }
        $kw_spiders = 'Indy|Mediapartners|Python-urllib|Yandex|alexa.com|Yahoo!|Googlebot|Bot|Crawl|Spider|spider|slurp|sohu-search|lycos|robozilla|ApacheBench';
        if (preg_match("/($kw_spiders)/i", $agentStr)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getUniqueId()
    {
        return md5(uniqid(mt_rand(), TRUE) . $_SERVER['REQUEST_TIME'] . mt_rand());
    }

    public static function getMemUsed()
    {
        return intval(memory_get_usage() / 1024);
    }

    public static function getBrowerAgent()
    {
        $trans = array(
            "[" => "{",
            "]" => "}"
        );
        $agentStr = empty($_SERVER['HTTP_USER_AGENT']) ? "" : $_SERVER['HTTP_USER_AGENT'];
        return strtr($agentStr, $trans);
    }

    public static function getBrowerAgentType($agentStr)
    {
        //$agentStr = empty($_SERVER['HTTP_USER_AGENT']) ? "" : $_SERVER['HTTP_USER_AGENT'];
        $agentType = "";
        if (stripos($agentStr, "MSIE") !== FALSE) {
            $agentType = "IE";
        } else if (stripos($agentStr, "Chrome") !== FALSE) {
            $agentType = "Chrome";
        } else if (stripos($agentStr, "Firefox") !== FALSE) {
            $agentType = "Firefox";
        } else if (stripos($agentStr, "iPad") !== FALSE) {
            $agentType = "iPad";
        } else {
            $agentType = "others";
        }
        return $agentType;
    }

    public static function DataToArray($dbData, $keyword, $allowEmpty = FALSE)
    {
        $retArray = array();
        if (is_array($dbData) == false or empty($dbData)) {
            return $retArray;
        }
        foreach ($dbData as $oneData) {
            if (isset($oneData[$keyword]) and empty($oneData[$keyword]) == false or $allowEmpty) {
                $retArray[] = $oneData[$keyword];
            }
        }
        return $retArray;
    }


    public static function changeDataKeys($data, $keyName, $toLowerCase = false)
    {
        $resArr = array();
        if (empty($data)) {
            return false;
        }
        foreach ($data as $v) {
            $k = $v[$keyName];
            if ($toLowerCase === true) {
                $k = strtolower($k);
            }
            $resArr[$k] = $v;
        }
        return $resArr;
    }

    public static function getClientIP($mode = "string")
    {
        $ip = \Air\Libs\Base\HttpRequest::getAirRequest()->ip;
        if ($mode != "string") {
            $ip = ip2long($ip);
        }
        return $ip;
    }

    public static function getServerIp()
    {
        return exec("ifconfig | grep 'inet' | grep -v inet6 | grep -v 127* | awk '{print $2}'|awk -F '/' '{print $1}'");
    }

    public static function unmark_amps($get)
    {
        if (!empty($get)) {
            foreach ($get as $param => $value) {
                if (preg_match('/^amp\;(.*)$/i', $param)) {
                    $paramNew = preg_replace('/^amp\;(.*)$/i', '$1', $param);
                    unset($get[$param]);
                    if ($paramNew != '') {
                        $get[$paramNew] = $value;
                    }
                }
            }
        }
        return $get;
    }

    public static function zaddslashes($string, $force = 0, $strip = FALSE)
    {
        if (!defined("MAGIC_QUOTES_GPC")) {
            define("MAGIC_QUOTES_GPC", "");
        }
        if (!MAGIC_QUOTES_GPC || $force) {
            if (is_array($string)) {
                foreach ($string as $key => $val) {
                    $string[$key] = \Air\libs\base\Utilities::zaddslashes($val, $force, $strip);
                }
            } else {
                $string = \Air\Libs\Base\Utilities::utf8Encode($string);
                $string = ($strip ? stripslashes($string) : $string);
                $string = htmlspecialchars($string, ENT_QUOTES);
            }
        }
        return $string;
    }

    /**
     * 将传入的字符串，转换成utf8
     * @author hailong
     */
    public static function utf8Encode($string)
    {
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = @mb_convert_encoding($string, 'UTF-8', 'ascii,GB2312,gbk,UTF-8');
            //$string= iconv("UTF-8", "UTF-8//IGNORE", $string);
        }
        return $string;
    }

    /**
     * 时间转换函数，将时间转换成几分钟前，几小时前的形式。
     * @param timestamp $createTime
     * @return    string $str
     */
    public static function timeStrConverter($createTime)
    {
        $now = date("Y-m-d");
        $yearNow = date("Y");
        $yearLast = date("Y", $createTime);
        if ($yearNow == $yearLast) {
            $timeValue = ceil((time() - $createTime) / 60);
            if ($timeValue < 0) {
                $timeValue = 0 - $timeValue;
                $str = "0分钟前";
            } elseif ($timeValue < 20) {
                $timeValue = ltrim($timeValue, '-');
                $str = " {$timeValue}分钟前 ";
            } elseif (date("m", $createTime) == date("m") && date("d", $createTime) == date("d")) { //一个月以内的时间
                $str = '今天 ' . date("G:i", $createTime);
            } elseif (date("m", $createTime) == date("m") && date("d", $createTime) == date("d") - 1) {
                $str = '昨天 ' . date("G:i", $createTime);
            } else { //今年内的时间 并且 一天以上的时间
                $str = date("m月d日 G:i", $createTime);
            }
        } else { //一年以上的时间
            $str = date("Y年m月d日 G:i", $createTime);
        }
        return $str;
    }


    public static function timeStrConverterDay($createTime)
    {
        $dayValue = ceil((time() - $createTime) / 86400);
        $weekValue = ceil((time() - $createTime) / (86400 * 7));
        $monthValue = ceil((time() - $createTime) / (86400 * 30));
        $yearValue = ceil((time() - $createTime) / (86400 * 360));
        if (date("m", $createTime) == date("m") && date("d", $createTime) == date("d")) { //一个月以内的时间
            $str = '今天 ' . date("G:i", $createTime);
        } elseif (date("m", $createTime) == date("m") && date("d", $createTime) == date("d") - 1) {
            $str = '昨天 ' . date("G:i", $createTime);
        } elseif ($dayValue < 8) {
            $str = $dayValue . '天前';
        } elseif ($weekValue <= 5) {
            $str = $weekValue . '周前';
        } elseif ($monthValue <= 12) {
            $str = $monthValue . '个月前';
        } else {
            $str = $yearValue . '年前';
        }
        return $str;
    }


    public static function timetoWeek($time)
    {
        $weekarray = array('日', '一', '二', '三', '四', '五', '六');
        $key = date('w', $time);
        $week = "星期" . $weekarray[$key];
        return $week;
    }

    public static function objectToArray($obj)
    {
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
            return array_map(array('self', __FUNCTION__), $obj);
        }
        return $obj;
    }

    /**
     * php in_array is too slow when array is large, this is optimized one
     * @author Chen Hailong
     */
    public static function inArray($item, $array)
    {
        $flipArray = array_flip($array);
        return isset($flipArray[$item]);
    }

    public static function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL)
    {
        $encoding = $encoding === NULL ? mb_internal_encoding() : $encoding;
        $pad_before = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
        $pad_after = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
        $pad_len -= mb_strlen($str, $encoding);
        $target_len = $pad_before && $pad_after ? $pad_len / 2 : $pad_len;
        $str_to_repeat_len = mb_strlen($pad_str, $encoding);
        $repeat_times = ceil($target_len / $str_to_repeat_len);
        $repeated_string = str_repeat($pad_str, max(0, $repeat_times)); // safe if used with valid utf-8 strings
        $before = $pad_before ? mb_substr($repeated_string, 0, floor($target_len), $encoding) : '';
        $after = $pad_after ? mb_substr($repeated_string, 0, ceil($target_len), $encoding) : '';
        return $before . $str . $after;
    }

    /*
     * 调用参数说明:$string：要截取的字符串,
     * $length:截取长度,
     * $etc:截取后跟随的字符串(要根据$fraction来设置),
     * $fraction每个英文字符作为多少个汉字处理(默认半个0.5)
     */
    public static function truncate_utf8($string, $length, $etc = '..', $fraction = 0.5)
    {
        $result = '';

        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'utf-8');

        for ($i = 0, $j = 0; $i < strlen($string); $i++) {
            if ($j >= $length) {
                for ($x = 0, $y = 0; $x < strlen($etc); $x++) {
                    if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                        $x += $number - 1;
                        $y++;
                    } else {
                        $y += 0.5;
                    }
                }
                $length -= $y;
                break;
            }

            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                $i += $number - 1;
                $j++;
            } else {
                $j += $fraction;
            }
        }

        for ($i = 0; (($i < strlen($string)) && ($length > 0)); $i++) {
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                if ($length < 1.0) {
                    break;
                }

                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            } else {
                $result .= substr($string, $i, 1);
                $length -= $fraction;
            }
        }

        $result = htmlentities($result, ENT_QUOTES, 'utf-8');
        return $result;
    }

    /**
     * 速度比array_diff快，但只支持两个数组。
     * @warn 因为多一个array_flip，内存占用峰值多一些。
     */
    public static function array_diff_fast($firstArray, $secondArray)
    {
        if (!is_array($firstArray) || !is_array($secondArray)) {
            return FALSE;
        }
        $secondArray = array_flip($secondArray);
        foreach ($firstArray as $key => $value) {
            if (isset($secondArray[$value])) {
                unset($firstArray[$key]);
            }
        }
        return $firstArray;
    }

    public static function sortArray($array, $order_by, $order_type = 'ASC')
    {
        if (!is_array($array)) {
            return array();
        }
        $order_type = strtoupper($order_type);
        if ($order_type != 'DESC') {
            $order_type = SORT_ASC;
        } else {
            $order_type = SORT_DESC;
        }

        $order_by_array = array();
        foreach ($array as $k => $v) {
            $order_by_array[] = $array[$k][$order_by];
        }
        array_multisort($order_by_array, $order_type, $array);
        return $array;
    }

    public static function safeDivision($numerator, $denominator)
    {
        if ($denominator == 0) {
            return FALSE;
        }
        return $numerator / floatval($denominator);
    }

    public static function utf8_encode_mix($input)
    {
        if (is_array($input)) {
            $result = array();
            foreach ($input as $k => $v)
                $result[$k] = \Air\Libs\Base\Utilities::utf8_encode_mix($v);
        } else
            is_numeric($input) ? $result = $input : $result = iconv("UTF-8", "UTF-8//IGNORE", $input);

        return $result;
    }

    /**
     * $option['is_json'] = 1 表示json参数类型
     */
    public static function curl($url, $params = array(), $options = array('timeout' => 0, 'is_post' => 0, 'need_decode' => 1, 'header' => array()))
    {
        $start = microtime(1);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $timeout = $options['timeout'] ? $options['timeout'] : 30;
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if (!empty($options['is_post'])) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            if (empty($options['is_json'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            } else {
                $options['header'][] = "Content-Type: application/json";
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_UNESCAPED_UNICODE));
            }
        }
        if (!empty($options['header']['Cookie'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $options['header']['Cookie']);
            unset($options['header']['Cookie']);
        }

        if (!empty($options['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
        }
        $SSL = strtolower(substr($url, 0, 8)) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        $result = curl_exec($ch);
        if (empty($result)) {
            $result = curl_exec($ch);
        }
        if (!isset($options['need_decode'])) {
            $options['need_decode'] = 1;
        }
        !empty($options['need_decode']) && $result = json_decode($result, 1);
        $code_type = 1;
        if (isset($result['code'])) {
            $code_type = \Phplib\Tools\CommonFun::algoCodes($result['code']);
        }
        if ($code_type > 1) {
            $result = curl_exec($ch);
            !empty($options['need_decode']) && $result = json_decode($result, 1);
        }
        $info = array();
        // $class = get_called_class();
        $end = microtime(1);
        $spend = $end - $start;
        $extra = [];
        if ($params['check_id']) {
            $extra = ['check_id' => $params['check_id']];
        }
        if (empty($result) || ($options['need_decode'] && isset($result['code']) && $result['code'] > 0)) {
            $info = curl_getinfo($ch);
            Logger::error($info, "curl_error", $extra);
            Logger::info(['code_detail', $url, $params, 'spend' => $spend, 'result' => $result], 'curl_error', $extra);
        }
        if ($spend > 3) {
            Logger::info([$url, $params, 'spend' => $spend, 'result' => $result], 'curl_info', $extra);
        }
        curl_close($ch);
        return $result;
    }

    public static function uploadFile($url, $filename, $key, $post_data)
    {
        if (version_compare(PHP_VERSION, '5.5.0') >= 0) {
            $file = new \CURLFile($filename, '', $key);
        } else {
            $file = "@$filename;filename=$key;type=application/pdf";
        }
        $post_data['file_contents'] = $file;
        Logger::info($post_data, 'upload_file');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        $result = curl_exec($ch);
        Logger::info($result, 'upload_file');
        if ($result) {
            $result = json_decode($result, 1);
        }
        curl_close($ch);
        return $result;
    }

    public static function encodeId($int)
    {
        $hex = dechex($int);
        $code = substr($hex, -2) . substr($hex, 2, -2) . substr($hex, 0, 2);
        return $code;
    }

    public static function decodeId($str)
    {
        $str = substr($str, -2) . substr($str, 2, -2) . substr($str, 0, 2);
        $int = hexdec($str);
        return $int;
    }

    public static function encodeNum($int)
    {
        $hex = dechex('5476' . $int);
        $code = substr($hex, -2) . substr($hex, 2, -2) . substr($hex, 0, 2);
        return $code;
    }

    public static function decodeNum($str)
    {
        $str = substr($str, -2) . substr($str, 2, -2) . substr($str, 0, 2);
        $int = substr(hexdec($str), 4);
        return $int;
    }

    static function patchUrl($url, $data, $header)
    {
        $data  = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $output = json_decode($output, 1);
        return $output;
    }

    public static function curlNew($url, $params = array(), $options = array('is_post' => 0, 'need_decode' => 1, 'header' => array()))
    {
        $start = microtime(1);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        if (!empty($options['is_post'])) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            if (empty($options['is_json'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            } else {
                $options['header'][] = "Content-Type: application/json";
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            }
        }
        if (!empty($options['header'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
        }
        $result = curl_exec($ch);
        $info = array();
        $class = get_called_class();
        if (empty($result)) {
            $result = curl_exec($ch);
        }
        if (empty($result)) {
            //$result = curl_exec($ch);
        }
        $resultJ = $result;
        !empty($options['need_decode']) && $result = json_decode($result, 1);
        if (empty($result)) {
            $info = curl_getinfo($ch);
            $debug = debug_backtrace();
            Logger::error([$url, $info, $debug], "url_error_curlNew");
        } else {
            $end = microtime(1);
            $spend = intval(($end - $start) * 1000);
            // Logger::error("--" . $url . "--\t--$spend--\t--$resultJ--", "time_curlNew");
        }
        curl_close($ch);
        return $result;
    }

    static private $ddm = [
        'op'        => "https://oapi.dingtalk.com/robot/send?access_token=3a827512f28e713716b35968edd2c5c39df0369a41ba19962fc33d2644afeb63",
        'bigop'     => "https://oapi.dingtalk.com/robot/send?access_token=4e7fa1e8d0fc7801510782f6d421b24629a10e6b816ce1c4cea2e25a0a17ffde",
        'dev'       => "https://oapi.dingtalk.com/robot/send?access_token=7e8f485e94501bfb5b5f5183806a9f29dae1764d1d5d17b6f60e1a9719a23e70",
        'test_yp'   => "https://oapi.dingtalk.com/robot/send?access_token=87765bb9a93f3b46eee09815c4a80a58f6ba7ee51f1213ae5235fbccf0c567b4",
        'cloudm'    => "https://oapi.dingtalk.com/robot/send?access_token=7f2b453a5dcdd9ef345c0d37cc3fe539cf9299ae56817def4af52d13918f483e",
        'cm_warning' => "https://oapi.dingtalk.com/robot/send?access_token=e72f31c3de8566fed694b80e3100ca121cbb7507d62f997084d0b8ef9d9752ce",
        'db_excep'  => "https://oapi.dingtalk.com/robot/send?access_token=5e22669dd1de380f3869cbb949cbce4329c5cf3bf99592440bf0efa7b5c2a266",
        'prod_yp'   => "https://oapi.dingtalk.com/robot/send?access_token=d981dbd2f10fcbf414c02a72ed631c6e01187f636fbaf288fb9f6cc996a6a8ac",
        'ns_lxd'    => "https://oapi.dingtalk.com/robot/send?access_token=3a3d7e28b13e20d1ce8533705c4ddd0ce09de4ab82f458d6766fba49fb070a33",
        'ns_svip'   => "https://oapi.dingtalk.com/robot/send?access_token=049b8d9517317c42581ec8a8798d9030b45c68c658d31bbc2fda3ece67e7d47c",
        'ns_akg'    => "https://oapi.dingtalk.com/robot/send?access_token=3f9325c2ca679fa56c24f2fdba1351927051306794c810ab59bbd48fadd04dfb",
        'ns_main'   => "https://oapi.dingtalk.com/robot/send?access_token=8690a6ed2d791260e02a430080f52fb996967ae4ecaf2bab74ec2813bde2abb3",
        'smb_ops'   => "https://oapi.dingtalk.com/robot/send?access_token=b82522b3125ae770ea4215ccdbccf6f3cbe352951d081b9b4ceafbc207afb47f",
        'debuger'   => "https://oapi.dingtalk.com/robot/send?access_token=ed6be0a68c2b62d950e317b53eb5bf5cd39b196b359dc04fcd0e72c6d2a6cd0e",
        'vvip'      => "https://oapi.dingtalk.com/robot/send?access_token=31e8c9af09730bd1a67a1fe79ed9d2c81aea9ac507bee4299043def2f00d9787",
        'FD16A'     => "https://oapi.dingtalk.com/robot/send?access_token=c237340ae8404c8860c0da18f12b59c2167ad946b05020b500b10a31fe192642",
        'FD16_back' => "https://oapi.dingtalk.com/robot/send?access_token=980425efd219c20a7ddafbfb6e6678b980b4534397ecc425243f48531378fa5c",
        'reid'      => "https://oapi.dingtalk.com/robot/send?access_token=e5f98fca9864f5513842fb0a3c7345fbd5ad38c800043cab8b0b1984f3dbc9ac",
        'fd16'      => "https://oapi.dingtalk.com/robot/send?access_token=861afbc22f2dd9bfa31a52d96aa3c0f1b0942e2e6b7afd70061dcc8d439529b5",
        'dr'        => "https://oapi.dingtalk.com/robot/send?access_token=8a5f7523fd3f2e5e351ce36e4b718cd197ae1a0627fea2cf9bae001290db9802",
        'sla'       => "https://oapi.dingtalk.com/robot/send?access_token=12e55e8bc47a907a16e24cd33034694d2a06cd6ad6c35036463c47b76bf86d7d",
        // SLA-Error报警群
        'sla_rd'    => "https://oapi.dingtalk.com/robot/send?access_token=1350fbac2e5427818c3ff9ef5de0bbb4fa660d21c70842aa31499c334ec785f2",
        '10k'       => "https://oapi.dingtalk.com/robot/send?access_token=d8825381079b845df4ad5aa90b6361dc0434ea21f6d9ae61d003654c95dd2e8d",
        'balance'   => "https://oapi.dingtalk.com/robot/send?access_token=8d4b82598eac0ed83b3a03078172dfd283002b5dd461df321d13c9d64e00b236",
        'self_check' => "https://oapi.dingtalk.com/robot/send?access_token=f8090324297472910ed6cb1340bab919f3ba66704d11268cca4df298d61bf73b",
        'ai_fd16'   => "https://oapi.dingtalk.com/robot/send?access_token=fcea4ba5a3183461138ca6e3d76ffecf3daea21a667398c1e4aa228293b82b25",
        // 配置培训 小丸子
        'config'    => "https://oapi.dingtalk.com/robot/send?access_token=3f4f3d9dbc465c22b2e82104d95ff7c82010899b1c9f73f96b07f45aab4943a7",
        'after_sale' => "https://oapi.dingtalk.com/robot/send?access_token=01e3d0c325d14e4e4d4fe07331d5201a8d8b895902b1dc812cbadac16c74eabf",
        'positive'  => "https://oapi.dingtalk.com/robot/send?access_token=87b2a00dccde1c242575983e45db80475ff275bf74aa1e047c3cceb38d8d93bd",
        'optic_papilledema' => "https://oapi.dingtalk.com/robot/send?access_token=fe0205c2e9494c4866eb88829308752239c58213f5969698f3cff6dfcd4b3ab8",
        'dr_v2'     => 'https://oapi.dingtalk.com/robot/send?access_token=f29d463180f59af5e504ce47cf5f981bc74d083074dc9c0ba1ca1d93e2587c32',
        // 账号配置群
        'config_ops' => 'https://oapi.dingtalk.com/robot/send?access_token=d8c76333744360430c59f867bf5eed2499c111bb59ae6cc7a2633f4aa0abfd05',
        // 相机更换报警群
        'camera_change' => 'https://oapi.dingtalk.com/robot/send?access_token=d3dd91f0c7dcaf2444f58e0846355c62378e667b43a6a3e0a06b8cf1e9e87824',
        'wuhushaicha' => 'https://oapi.dingtalk.com/robot/send?access_token=d244e697f6afb0e106a7a68dca3da14068cf31a4bce8df5486460aa26ddb6621',
        //cp报警监控群
        'cp_warning' => "https://oapi.dingtalk.com/robot/send?access_token=67bfa1d80681bd0c34b95165cc644c90d6538d6560eb63636d66458b7aa859d1",
    ];
    public static function DDMonitor($content, $group, $at = FALSE)
    {
        if (ENV == 'production' && in_array($group, ['debuger']) && !$at) {
            return;
        }
        if (strpos('pre' . $group, 'ns_')) {
            $content = "【NS】" . $content;
        }
        if (strpos('pre' . $group, 'ns_') && ENV != 'production') {
            $group = 'test_yp';
        } elseif (ENV != 'production' && in_array($group, ['FD16_back'])) {
            return false;
        } elseif (ENV != 'production' && !in_array($group, ['debuger', 'vvip', 'test_yp', 'db_excep'])) {
            $group = 'dev';
        }
        if ($group == 'dev' || $group == 'debuger') {
            $group = 'debuger';
            if (!strpos('begin' . $content, 'Debuger')) {
                $content .= "【Debuger】";
            }
        }
        if ($group == 'cloudm' && strpos('pre' . $content, 'P3')) {
            $group = 'cm_warning';
        }
        if ($group == 'positive') {
            $content = "【阳性跟踪】\n" . $content;
        }
        if (is_array($at)) {
            $data = ['msgtype' => 'text', 'text' => ['content' => $content], 'at' => ['isAtAll' => 0, 'atMobiles' => $at]];
        } else {
            $data = ['msgtype' => 'text', 'text' => ['content' => $content], 'at' => ['isAtAll' => $at]];
        }
        // $data_string = json_encode($data);
        $content_log = str_replace("\n", ";", $content);
        Logger::error($content_log, 'dingding_alarm');
        $ret = self::curl(self::$ddm[$group], $data, ['is_json' => 1, 'is_post' => 1, 'need_decode' => 1, 'header' => ['Content-type: application/json']]);
        if ($ret['errcode']) {
            Logger::error($ret, 'dingding_alarm_error');
            if (time() % 3600 == 1 && date('H') > '09' && date('H') < '18') {
                $data = ['content' => ENV . "-钉钉报警报错" . json_encode($ret, JSON_UNESCAPED_UNICODE), 'phone' => '13811885439'];
                Sms::smsRecord($data);
            }
        }
        // TODO 钉钉报警失败处理
        // Logger::error($content, 'dingding_alarm');
        // Logger::error($content, 'dingding_alarm_return');
        return $ret;
    }

    public static function decodeAmp($string)
    {
        $pairs = array("&amp;" => "&");
        return strtr($string, $pairs);
    }

    /**
     * 汉字，字母，下划线，. @
     */
    public static function hasInvalidCharacter($str)
    {
        $str = trim($str);
        if (empty($str)) {
            return TRUE;
        }
        if (!preg_match("/^[\x{0391}-\x{ffe5}\w\., \-_\?@!#]+$/u", $str)) {
            return FALSE;
        }
        return TRUE;
    }

    public static function checkStr($str)
    {
        $res = preg_match('/^[A-Za-z0-9_\-]{3,20}$/', $str);
        return $res ? TRUE : FALSE;
    }

    //过滤emoji表情
    public static function RemoveEmoji($text)
    {
        if (empty($text)) {
            return $text;
        }
        return preg_replace('/([0-9|#][\x{20E3}])|[\x{00ae}|\x{00a9}|\x{203C}|\x{2047}|\x{2048}|\x{2049}|\x{3030}|\x{303D}|\x{2139}|\x{2122}|\x{3297}|\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $text);
    }

    /**
     * 转换电话号码，保留前3位和后四位，其他都用*代替
     * @param string|int $phone
     * @return string $phone
     */
    public static function convertPhone($phone)
    {
        \Phplib\Tools\Logger::debug($phone, 'phone.convert');
        $phone = trim($phone);
        if (!$phone) {
            return $phone;
        }
        $length = strlen($phone);
        $length = $length - 3 - 4;
        if ($length < 0) {
            $length = 0;
        }
        $phone = substr($phone, 0, 3) . str_repeat('*', $length) . substr($phone, -4);
        return $phone;
    }

    public static function isPhone($phone)
    {
        if (!preg_match('/^1[0-9]{10}$/', $phone)) {
            return FALSE;
        }
        return TRUE;
    }
    public static function isTelephone($phone)
    {
        if (!preg_match('/^[0-9]{7,8}$/', $phone)) {
            return FALSE;
        }
        return TRUE;
    }

    public static function getGenderfromIDCard($id)
    {
        if (self::is_idcard($id)) {
            if (strlen($id) == 18) {
                return substr($id, -2, 1) % 2 ? 1 : 2;
            } else {
                return substr($id, -1, 1) % 2 ? 1 : 2;
            }
        }

        return 0;
    }

    public static function getBirthdayFromIDcard($id)
    {
        if (self::is_idcard($id)) {
            if (strlen($id) == 18) {
                $ymd = substr($id, 6, 8);
                $century = substr($id, 6, 2);
                if ($century != '19' && $century != '20') {
                    $ymd = '19' . substr($id, 8, 6);
                }
            } else {
                $ymd = '19' . substr($id, 6, 6);
            }

            return date('Y-m-d', strtotime($ymd));
        }

        return '';
    }

    public static function is_idcard($id)
    {
        $id = strtoupper($id);
        $regx = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = array();
        if (!preg_match($regx, $id)) {
            return FALSE;
        }
        if (15 == strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return FALSE;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b = (int)$id[$i];
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id, 17, 1)) {
                    return FALSE;
                } //phpfensi.com
                else {
                    return TRUE;
                }
            }
        }
    }

    /**
     * 对提供的数据进行urlsafe的base64编码。
     *
     * @param string $data 待编码的数据，一般为字符串
     *
     * @return string 编码后的字符串
     * @link http://developer.qiniu.com/docs/v6/api/overview/appendix.html#urlsafe-base64
     */
    static public function base64_urlSafeEncode($data)
    {
        $find = array('+', '/');
        $replace = array('-', '_');
        return str_replace($find, $replace, base64_encode($data));
    }

    static public function a2o($appid)
    {
        return intval(($appid - 820000) / 10);
    }

    static public function o2a($org_id)
    {
        return $org_id * 10 + 820000 + $org_id % 5;
    }

    static public function renderTimeString($date)
    {
        if ($date > date('Y-m-d 00:00:00')) {
            return "今天" . date('H:i', strtotime($date));
        } else {
            $day_num = ceil((time() - strtotime($date)) / 86400);
            return $day_num . "天前";
        }
    }

    static public function getClient()
    {
        $agent = \Air\Libs\Base\HttpRequest::getAirRequest()->agent;
        if (strpos($agent, 'Windows NT')) {
            return 'windows-pc';
        } elseif (strpos($agent, 'iPad')) {
            return 'ipad';
        } elseif (strpos($agent, 'iPhone')) {
            return 'iphone';
        } elseif (strpos($agent, 'Macintosh')) {
            return 'Mac';
        } elseif (strpos($agent, 'Android')) {
            return 'android';
        } else {
            return '';
        }
    }
    public static function cdcNumber($int, $prefix = '8989')
    {
        if ($prefix == 'TYE' || $prefix == 'TYD' || $prefix == 'TYC') {
            $new_int = $int % 10 . decoct($int * 3 + 7);
        } else {
            //$new_int = decoct($int + 7);
            $new_int = $int % 10 . $int;
        }
        return $prefix . $new_int;
    }

    public static function isWrongName($name, $secand = 0, $pis_name = '')
    {
        $name = self::sbc2Dbc($name);
        $pis_name = self::sbc2Dbc($pis_name);
        if (!preg_match("/[\x80-\xff]/", mb_substr($name, 1))) {
            //return 1;
        }
        if ($secand && !preg_match("/[\x80-\xff]/", mb_substr($name, 0, 1))) {
            return 2;
        }
        if ($pis_name && mb_substr($name, 0, 1) != mb_substr($pis_name, 0, 1)) {
            //return 3;
        }
        return 0;
    }

    public static $wrong_name_wording = [
        1 => '名中需至少包含一个汉字（不包括姓氏）',
        2 => '姓需为汉字',
        3 => '姓与PIS系统不相符',
    ];

    static public function sbc2Dbc($str)
    {
        $arr = array(
            '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4', '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
            'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E', 'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
            'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O', 'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
            'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y', 'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
            'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i', 'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
            'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's', 'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
            'ｙ' => 'y', 'ｚ' => 'z',
            '（' => '(', '）' => ')', '〔' => '(', '〕' => ')', '【' => '[', '】' => ']', '〖' => '[', '〗' => ']', '“' => '"', '”' => '"',
            '‘' => '\'', '’' => '\'', '｛' => '{', '｝' => '}', '《' => '<', '》' => '>', '％' => '%', '＋' => '+', '—' => '-', '－' => '-',
            '～' => '~', '：' => ':', '。' => '.', '、' => ',', '，' => ',', '、' => ',',  '；' => ';', '？' => '?', '！' => '!', '…' => '-',
            '‖' => '|', '”' => '"', '’' => '`', '‘' => '`', '｜' => '|', '〃' => '"', '　' => ' ', '×' => '*', '￣' => '~', '．' => '.', '＊' => '*',
            '＆' => '&', '＜' => '<', '＞' => '>', '＄' => '$', '＠' => '@', '＾' => '^', '＿' => '_', '＂' => '"', '￥' => '$', '＝' => '=',
            '＼' => '\\', '／' => '/'
        );
        return strtr($str, $arr);
    }

    /**
     * 使用ffmpeg获取视频信息
     * @param  String $file 视频文件
     * @return Array
     */
    public static function getVideoInfo($file)
    {
        ob_start();
        passthru(sprintf('ffmpeg -i "%s" 2>&1', $file));
        $video_info = ob_get_contents();
        ob_end_clean();
        // 使用输出缓冲，获取ffmpeg所有输出内容
        $ret = [];
        // Duration: 00:33:42.64, start: 0.000000, bitrate: 152 kb/s
        if (preg_match("/Duration: (.*?), start: (.*?), bitrate: (\d*) kb\/s/", $video_info, $matches)) {
            $ret['duration'] = $matches[1]; // 视频长度
            $duration = explode(':', $matches[1]);
            $ret['duration'] = $duration[0] * 3600 + $duration[1] * 60 + $duration[2]; // 转为秒数
            $ret['start'] = $matches[2]; // 开始时间
            $ret['bitrate'] = $matches[3]; // bitrate 码率 单位kb
        }

        // Stream #0:1: Video: rv20 (RV20 / 0x30325652), yuv420p, 352x288, 117 kb/s, 15 fps, 15 tbr, 1k tbn, 1k tbc
        if (preg_match("/Video: (.*?), (.*?), (.*?)[,\s]/", $video_info, $matches)) {
            $ret['vcodec'] = $matches[1];     // 编码格式
            $ret['vformat'] = $matches[2];    // 视频格式
            $ret['resolution'] = $matches[3]; // 分辨率
            list($width, $height) = explode('x', $matches[3]);
            $ret['width'] = $width;
            $ret['height'] = $height;
        }

        // Stream #0:0: Audio: cook (cook / 0x6B6F6F63), 22050 Hz, stereo, fltp, 32 kb/s
        if (preg_match("/Audio: (.*), (\d*) Hz/", $video_info, $matches)) {
            $ret['acodec'] = $matches[1];      // 音频编码
            $ret['asamplerate'] = $matches[2]; // 音频采样频率
        }

        if (isset($ret['seconds']) && isset($ret['start'])) {
            $ret['play_time'] = $ret['seconds'] + $ret['start']; // 实际播放时间
        }

        $ret['size'] = filesize($file); // 视频文件大小
        //$video_info = iconv('gbk','utf8', $video_info);
        return $ret;
    }

    static public function validateEmail($email)
    {
        $result = trim($email);
        if (filter_var($result, FILTER_VALIDATE_EMAIL)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    function randomFloat($scale = 0, $max = 1, $min = 0)
    {
        return round($min + mt_rand() / mt_getrandmax() * ($max - $min), $scale);
    }

    //下载文件夹
    public static function getFile($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename
        );
    }
    /**
     * 对比两个数组，最多支持二维
     * @param $old 原值
     * @param $new 新值
     * @param $array_filed 属性中为数组的字段
     * @param $event 操作标记
     * @return array
     */
    public static function diffFileds($old = [], $new = [], $array_filed = [], $event = '')
    {
        $event && $diff['event'] = $event;
        foreach ($new as $key => $val) {
            //若值为空则跳过
            // if (is_null($val) || $val == '') {
            //     continue;
            // }
            // 若对比的字段为数组，则特殊处理
            if (in_array($key, $array_filed)) {
                $tmp_old_val = !is_array($old[$key]) ? json_decode($old[$key], true) : $old[$key];
                $tmp_new_val = !is_array($val) ? json_decode($val, true) : $val;
                $diff[$key] = self::diffFileds($tmp_old_val, $tmp_new_val);
            } else {
                is_array($val) ?? $val = json_encode($val);
                if (isset($old[$key])) {
                    if ($old[$key] != $val) {
                        $diff[$key]['old'] = $old[$key];
                        $diff[$key]['new'] = $val;
                    }
                } else {
                    $diff[$key]['old'] = null;
                    $diff[$key]['new'] = $val;
                }
            }
        }
        return $diff;
    }
    static public function getLocale($locale)
    {
        $locale_map = self::getLocaleList();
        if (isset($locale_map[$locale])) {
            return $locale_map[$locale];
        }
        return '';
    }

    static public function getLocaleList($is_values = 0)
    {
        $locale_map = array(
            'en_US' => 'en_US', 'en-US' => 'en_US',
            'it-IT' => 'it_IT', 'it_IT' => 'it_IT',
            'vi-VN' => 'vi_VN', 'vi_VN' => 'vi_VN',
            'ru' => 'ru_RU', 'ru-RU' => 'ru_RU', 'ru_RU' => 'ru_RU',
            'ar_KW' => 'ar_KW', 'ar-KW' => 'ar_KW',
            'id-ID' => 'id_ID', 'id_ID' => 'id_ID',
            'es-CL' => 'es_CL', 'es_CL' => 'es_CL',
            'de-DE' => 'de_DE', 'de_DE' => 'de_DE',
            'zh-TW' => 'zh_TW', 'zh_TW' => 'zh_TW',
            'cs-CZ' => 'cs_CZ', 'cs_CZ' => 'cs_CZ',
        );
        if ($is_values) {
            return array_values(array_unique($locale_map));
        }
        return $locale_map;
    }

    //国际化多语言设置
    public static function setI18n($lang)
    {
        $locale = trim($lang);
        $locale = self::getLocale($locale);
        putenv('LANG=' . $locale);
        setlocale(LC_ALL, $locale . ".utf8");
        bindtextdomain('i18n', ROOT_PATH . '/resource/language');
        bind_textdomain_codeset('i18n', 'UTF-8');
        textdomain('i18n');
    }

    //恢复到中文语言包
    public static function restoreI18n()
    {
        putenv('LANG=');
        setlocale(LC_ALL, ".utf8");
        bindtextdomain('i18n', '');
        bind_textdomain_codeset('i18n', 'UTF-8');
        textdomain('i18n');
    }

    /**
     * BAEQ-2745-EN 是否包含中文汉字,
     * @return boolean  true 表示有中文汉字；false 表示没有中文
     */
    static public function hasChinese($string)
    {
        return (bool) preg_match("/[\x7f-\xff]/", $string);
    }

    /**
     * @param mixed $array
     * BAEQ-2745-EN 递归数组遍历翻译
     */
    static public function gettextRecursion($array, $keys = [], $parent_key = '')
    {
        if (is_string($array)) {
            return gettext($array);
        }
        if (is_array($array) && $array) {
            foreach ($array as $k => &$item) {
                if ($item && is_string($item) && (self::hasChinese($item) || strpos($item, 'ttps://img')) && (in_array($k, $keys) || is_numeric($k) && in_array($parent_key, $keys))) {
                    // Logger::info($item, 'gettext_info'); // 日志量太大，暂时注释掉。
                    $item = gettext($item);
                } elseif ($item && is_array($item)) {
                    $item = self::gettextRecursion($item, $keys, $k);
                }
            }
        }
        return $array;
    }

    static public function getLocaleDateFormat($locale, $type = 'ym')
    {
        if ($type == 'ym') {
            $map = [
                'en_US' => ' F, Y',
                'it_IT' =>  ' Y.m',
                'vi_VN' => ' Y.m',
                'zh_CN' => 'Y年m月',
                'ru_RU' => ' Y.m',
                'ar_KW' => ' Y.m',
                'id_ID' => ' Y.m',
                'es_CL' => ' Y.m',
                'de_DE' => ' Y.m',
                'zh_TW' => ' Y.m',
                'cs_CZ' => ' Y.m',
            ];
        } else {
            $map = [
                'en_US' => ' F, d, Y',
                'it_IT' =>  ' Y.m.d',
                'vi_VN' => ' Y.m.d',
                'zh_CN' => 'Y-m-d',
                'ru_RU' => ' Y.m.d',
                'ar_KW' => ' Y.m.d',
                'id_ID' => ' Y.m.d',
                'es_CL' => ' Y.m.d',
                'de_DE' => ' Y.m.d',
                'zh_TW' => ' Y.m.d',
                'cs_CZ' => ' Y.m.d',
            ];
        }
        if (isset($map[$locale])) {
            return $map[$locale];
        }
        return $map['zh_CN'];
    }

    /**
     * 根据gender转换成中文性别
     */
    public static function numToGender($num)
    {
        switch ($num) {
            case 1:
                return '男';
                break;
            case 2:
                return '女';
                break;
            default:
                return '未知';
        }
    }
    /**
     * 获取指定keys的数组
     * @param array $array 数据数组
     * @param array $get_keys 需要获取的字段，结构参照原数组
     * @param int $array_type 0：一维数组，1：二维数组
     * @param int $type 0：获取交集，1：获取差集
     * @return array
     */
    public static function getSpecifiedArray($array = [], $get_keys = [], $array_type = 0, $type = 0)
    {
        if (!$get_keys) {
            return $array;
        }
        $new = $data = [];
        if ($array_type == 1) {
            foreach ($array as $val) {
                $data[] = self::getSpecifiedArray($val, $get_keys, 0, $type);
            }
            return $data;
        }
        $get_keys = array_flip($get_keys);
        if ($type == 0) {
            $new = array_intersect_key($array, $get_keys);
        } else {
            $new = array_diff_key($array, $get_keys);
        }
        return $new;
    }

    /**
     * YTI18N-103 兼容海外俄语越南语小数点
     */
    public static function convertNumberI18n($number)
    {
        if (is_float($number) == FALSE) {
            return $number;
        }
        $number = (string)$number;
        $number = str_replace(',', '.', $number);
        return $number;
    }

    /**
     * 合并两个字符串，国际化处理
     */
    public static function concatI18n($string1, $string2)
    {
        $lang = getenv('LANG');
        if (in_array($lang, ['zh_TW', 'zh_CN']) || $lang == '') {
            return $string1 . $string2;
        }

        return $string1 . ' ' . lcfirst($string2);
    }

    static public function base64_url_encode($data): string
    {
        return strtr(base64_encode($data), '+/=', '-_,');
    }

    static public function base64_url_decode($data)
    {
        return base64_decode(strtr($data, '-_,', '+/='));
    }
}
