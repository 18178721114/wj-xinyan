<?php
namespace Air\Unit\Patient_name;

/**
 * php public/unit.php Patient_name\\Substring
 */
class Substring {
    public function __construct($arg) {
        $this->args = $arg;
    }
    
    public function run() {
        $cases = [
          '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', // 62位
          '0123456789', // 10位
          'abcdefghijklmnopqrstuvwxyz', // 26位
          'ABCDEFGHIJKLMNOPQRSTUVWXYZ', // 26位
          '! "#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', // 33位
          '一二三四五六七八九十', // 十位
          '一二三四五六七八九十一二三四五', // 十五位
          '一二三四五六七八九十a一二三四五', // 十六位
          '一二三四五六七八九十一二三四五六', // 十六位
          '一二三四五六七八九十一二三四五a', // 十六位
          '0123456789abcdefghijklmnopqrstuvwxyz! "#$%&\'()', // 46位
          '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ*+,-./:;<=', // 46位
          'abcdefgABCDEFGHIJKLMNOPQRSTUVWXYZ>?@[\\]^_`{|}~', // 46位
          '0123456789abcdefghijklmnopqrstuvwxy一"#$%&\'()', // 46位
          '一2二a三B四 五!六\\七\'八@九-十&张==', // 46位
          '一2二a三B四 五!六\\七\'八@九-十&张李', // 46位
          '长度的测试长度的测试长度的测试', // 15位
        ];
        foreach ($cases as $case) {
            $column = 'name';
            $data = [];
            $data[$column] = $case;
            if (!strpos($column, 'number_crypt') && strlen($data[$column]) > 45) {
                if (preg_match('/[\x80-\xff]/', $data[$column])) {
                    $data['extra_json']['suf_' . $column] = mb_substr($data[$column], 15);
                    $data[$column] = mb_substr($data[$column], 0, 15);
                } else {
                    $data['extra_json']['suf_' . $column] = substr($data[$column], 45);
                    $data[$column] = substr($data[$column], 0, 45);
                }
                // if (preg_match('/[\x80-\xff]/', $data[$column]) && !preg_match('/[\x01-\x79]/', $data[$column])) {
                //     // 不含 ascii
                //     $data['extra_json']['suf_' . $column] = mb_substr($data[$column], 15);
                //     $data[$column] = mb_substr($data[$column], 0, 15);
                // } elseif (!preg_match('/[\x80-\xff]/', $data[$column])) {
                //     // 只含 ascii
                //     $data['extra_json']['suf_' . $column] = substr($data[$column], 45);
                //     $data[$column] = substr($data[$column], 0, 45);
                // } else {
                //     $data['extra_json']['suf_' . $column] = mb_substr($data[$column], 15);
                //     $data[$column] = mb_substr($data[$column], 0, 15);
                // }
            }
            var_dump(
              $data,
            );
        }
    }
}
