<?php

const FIRST_LIST = [
    "modules",
    "package",
    "scripts",
    "libs",
    "mq",
];

list($path, $class_type) = inputPath();
list($class, $namespace) = path2Class($path);
switch ($class_type) {
    case "modules" :
        $content = <<<'EOT'
<?php
namespace %s;

class %s extends \Air\Libs\Controller
{
    public $must_login = FALSE;

    public function run() {
        $request = $this->request->REQUEST;
        if (!$this->_init()) {
            return false;
        }

        $this->setView(0, 'success', '');
    }

    private function _init() {
        $request = $this->request->REQUEST;
        if (!$request) {
            $this->setView($this->error_code_prefix . '01', '缺少参数', '');
            return FALSE;
        }
        return TRUE;
    }
}

EOT;
        $content = sprintf($content, $namespace, $class);
        break;
    case "dbhelper" :
        $content = <<<'EOT'
<?php
namespace %s;

class %s extends \Phplib\Db\DBModel
{
    const _DATABASE_ = 'ophthalmology';
    const _TABLE_ = '%s';
    public static $primary_key = '';
    public static $json_fields = [];
    public static $fields = array(

    );

}

EOT;
        $content = sprintf($content, $namespace, $class, toUnderScore(getDBName($class), 0));
        break;
    case "redis" :
        $content = <<<'EOT'
<?php
namespace %s;

class %s extends \Phplib\Redis\Redis {
    protected static $prefix = '%s';
    const EXPIRE_TIME = 86400;

}

EOT;
        $content = sprintf($content, $namespace, $class, $class);
        break;
    case "scripts" :
        $content = <<<'EOT'
<?php
namespace %s;

class %s {

    public function __construct($arg) {
        $this->args = $arg;
    }

    public function run()
    {

    }

}

EOT;
        $content = sprintf($content, $namespace, $class);
        break;
    case "package" :
    case "libs" :
    default :
        $content = <<<'EOT'
<?php
namespace %s;

class %s {

    public function __construct() {

    }

}

EOT;
        $content = sprintf($content, $namespace, $class);
        break;
}
makeFile($path, $content);
exit;

function makeFile($path, $content) {
    if (file_exists($path)) {
        echo "[{$path}]文件已存在！", PHP_EOL;
        return FALSE;
    }
    $path_info = pathinfo($path);
    $dirnames = explode("/", $path_info['dirname']);
    $basename = $path_info['basename'];
    $current_dirname = "";
    foreach ($dirnames as $dirname) {
        $current_dirname .= ($dirname . "/");
        if (!is_dir($current_dirname)) {
            mkdir($current_dirname, 0755);
        }
    }
    file_put_contents($path, $content);
    echo "[{$path}]文件创建成功！", PHP_EOL;
    return TRUE;
}

function path2Class($path) {
    $array = explode("/", $path);
    $class = substr(array_pop($array), 0, -10);
    $namespace = "Air";
    foreach ($array as $path) {
        $namespace .= "\\" . ucfirst($path);
    }
    return [$class, $namespace];
}

function inputPath() {
    echo "******************************", PHP_EOL;
    echo "1）选择创建类型；", PHP_EOL;
    echo "2）输入文件夹；", PHP_EOL;
    echo "3）输入类名；", PHP_EOL;
    echo "******************************", PHP_EOL;
    // 1）选择创建类型；
    do {
        echo "请选择创建类型：", PHP_EOL;
        foreach (FIRST_LIST as $key => $first_item) {
            if (!$key) {
                echo "* {$key}. {$first_item}", PHP_EOL;
            } else {
                echo "  {$key}. {$first_item}", PHP_EOL;
            }
        }
        $line = readline("请输入编号或根路径名：");
    } while (!($first = checkFirst(trim($line))));
    $path = implode("/", $first) . "/";
    $last_path = end($first);
    echo "当前路径：{$path}", PHP_EOL;
    // 2）输入文件夹；
    do {
        $line = readline("继续输入路径名？（Path/n）：");
        if (strtolower($line) == "n") {
            $stop = TRUE;
        } else {
            list($data, $message) = analyse($line);
            if (!$data) {
                echo $message, PHP_EOL;
            } else {
                $last_path = end($data);
                $path .= implode("/", $data) . "/";
            }
            $stop = FALSE;
        }
        echo "当前路径：{$path}", PHP_EOL;
    } while (!$stop);
    // 3）输入类名；
    do {
        switch ($first[0]) {
            case "modules" :
                $class_type = $first[0];
                $line = readline("请输入API Class名：");
                break;
            case "scripts" :
                $class_type = $first[0];
                $line = readline("请输入Script Class名：");
                break;
            case "libs" :
                $class_type = $first[0];
                $line = readline("请输入Libs Class名：");
                break;
            case "package" :
                if ($last_path == "helper") {
                    echo "请选择创建Helper类型：", PHP_EOL;
                    echo "* 0. dbhelper", PHP_EOL;
                    echo "  1. redis", PHP_EOL;
                    $line = readline("请输入编号：");
                    if ($line == 1) {
                        $class_type = "redis";
                        $line = readline("请输入Redis Class名：");
                    } else {
                        $class_type = "dbhelper";
                        $line = readline("请输入DBHelper Class名：");
                    }
                    break;
                }
                $class_type = $first[0];
                $line = readline("请输入Package Class名：");
                break;
            case "mq" :
                echo "请选择创建 MQ 类型：", PHP_EOL;
                echo "* 0. consumer", PHP_EOL;
                echo "  1. produser", PHP_EOL;
                $line = readline("请输入编号：");
                if ($line == 1) {
                    $class_type = "consumer";
                    $line = readline("请输入Consumer Class名：");
                } else {
                    $class_type = "produser";
                    echo "Produser 的 Topic 为 " . strtotime($last_path), PHP_EOL;
                    $line = 'MQ_' . $last_path . '_produser';
                }
                break;
            default :
                $class_type = "other";
                $line = readline("请输入Class名：");
                break;
        }
        list($data, $message) = analyse($line, $class_type);
        if (!$data) {
            $stop = FALSE;
            echo $message, PHP_EOL;
        } else {
            $stop = TRUE;
            $path .= implode("/", $data) . ".class.php";
        }
        echo "当前路径：{$path}", PHP_EOL;
    } while (!$stop);
    return [$path, $class_type];
}

function checkFirst($line) {
    if (is_numeric($line) && in_array($line, array_keys(FIRST_LIST))) {
        return [FIRST_LIST[$line]];
    } elseif (is_int($line)) {
        echo "[{$line}]不合法，请输入合法的编号！", PHP_EOL;
        return FALSE;
    } else {
        list($data, $message) = analyse($line);
        if (!$data) {
            echo $message, PHP_EOL;
            return FALSE;
        } else {
            return $data;
        }
    }
}

function analyse($str, $end_type = "") {
    if (!$str) {
        return [FALSE, "输入为空！"];
    }
    $arr = explode("/", $str);
    $count = count($arr);
    foreach ($arr as $key => $item) {
        $item = trim($item);
        $end = $key == $count - 1;
        if (!preg_match("/^[a-zA-Z][A-Za-z0-9_]+$/", $item)) {
            return [FALSE, "[{$item}]不合法，请输入合法的" . ($end_type ? "路径名" : "类名")];
        }
        if ($end && $end_type == "redis" && !preg_match("/^Redis[a-z0-9]+$/i", $item)) {
            return [FALSE, "[{$item}]不合法，请输入合法的Redis类名(Redis*****)"];
        } elseif ($end && $end_type == "dbhelper" && !preg_match("/^DB[a-z0-9_]+Helper$/i", $item)) {
            return [FALSE, "[{$item}]不合法，请输入合法的DBHelper类名(DB*****Helper)"];
        } elseif ($end && $end_type == "consumer" && !preg_match("/^MQ[a-z0-9_]+Consumer$/i", $item)) {
            return [FALSE, "[{$item}]不合法，请输入合法的consumer类名(MQ*****Consumer)"];
        }
        $data[] = getPath($item, $end, $end_type);
    }
    return [$data, implode("/", $data)];
}

function getPath($str, $end = FALSE, $end_type = "") {
    if ($end) {
        switch ($end_type) {
            case "redis" : return toRedis($str); // redis
            case "dbhelper" : return toDBHelper($str); // dbhelper
            case "package" :
            case "scripts" :
            case "libs" :
            case "other" :
              return toCamelCase($str);
            case "modules" : return toUnderScore($str); // api 驼峰转下划线
        }
    }
    return strtolower($str); // 文件夹名只能是小写
}

function toRedis($str) {
    return "Redis" . toCamelCase(substr($str, 5));
}

function toDBHelper($str) {
    return "DB" . toCamelCase(getDBName($str)) . "Helper";
}

function getDBName($str) {
    return substr(substr($str, 2), 0, -6);
}

function toUnderScore($str, $up = 1) {
    $dstr = preg_replace_callback("/([A-Z]+)/",function($matchs) {
        return "_".strtolower($matchs[0]);
    }, $str);
    if ($up) {
        return ucfirst(trim(preg_replace("/_{2,}/","_",$dstr),"_"));
    }
    return trim(preg_replace("/_{2,}/","_",$dstr),"_");
}

function toCamelCase($str) {
    $array = explode("_", $str);
    $result = ucfirst($array[0]);
    $len = count($array);
    if($len > 1) {
        for($i=1; $i<$len; $i++) {
            $result .= ucfirst($array[$i]);
        }
    }
    return $result;
}
