<?php
namespace Air\Libs;

trait ParamValidate
{
    public function _init_param()
    {
        $request = $this->request->REQUEST;
        $i = 0;
        // 管理权限
        // if ($this->must_admin) {
        //     if (!$this->isAdmin()) {
        //         $suffix = str_pad($i, 2, 0, STR_PAD_LEFT);
        //         $this->setView($this->error_code_prefix . $suffix, '后台权限不够', '');
        //         return FALSE;
        //     }
        // }
        // 必填参数的key和名称
        if ($this->param_required) {
            foreach ($this->param_required as $key => $val) {
                $i++;
                $suffix = str_pad($i, 2, 0, STR_PAD_LEFT);
                if (!$request[$key]) {
                    $this->setView($this->error_code_prefix . $suffix, '缺少参数' . $val, '');
                    return FALSE;
                }
            }
        }
        // 被加密的参数
        if ($this->param_crypted) {
            foreach ($this->param_crypted as $val) {
                $i++;
                $suffix = str_pad($i, 2, 0, STR_PAD_LEFT);
                $crypt_status = $this->paramDecrypt($val);
                if (!$crypt_status) {
                    $this->setView($this->error_code_prefix . $suffix, "缺少{$val}参数解密失败。", []);
                    return FALSE;
                }
            }
        }
        // 枚举类型符合枚举范围内的值
        if ($this->param_enum) {
            foreach ($this->param_enum as $key => $valid_values) {
                $i++;
                $suffix = str_pad($i, 2, 0, STR_PAD_LEFT);
                // 枚举类型符合枚举范围内的值
                if ($request[$key] && !in_array($request[$key], $valid_values)) {
                    $name = $this->param_required[$key] ?? $key;
                    $this->setView($this->error_code_prefix . $suffix, "参数{$name}不符合规范。", '');
                    return FALSE;
                }
            }
        }
        // 数字类型符合枚举范围内的值
        if ($this->param_numeric) {
            foreach ($this->param_numeric as $key => $val) {
                $i++;
                $suffix = str_pad($i, 2, 0, STR_PAD_LEFT);
                if ($request[$key] && !is_numeric($request[$key])) {
                    $name = $this->param_required[$key] ?? $key;
                    $this->setView($this->error_code_prefix . $suffix, "参数{$name}不符合规范，请输入数字类型的值。", '');
                    return FALSE;
                }
            }
        }
        return TRUE;
    }
    protected function paramDecrypt($field)
    {
        $login_str = $this->request->REQUEST[$field];
        // 倒数第4、倒数第3移替换掉第3、4。
        $login_str = substr($login_str, 0, 2) . substr($login_str, -4, 2) . substr($login_str, 4, -4) . substr($login_str, -2);
        $this->$field  = Xcrypt::decryptAes($login_str, LOGIN_SK);
        if (!$this->$field) {
            return FALSE;
        }
        return TRUE;
    }
}