<?php

namespace Air\Libs\Base;

class Config extends \Phplib\Config {

    public static function load($name) {
        if (is_null(self::$config_namespace)) {
            throw new \Exception("Config namespace is not set yet.");
        }

        static $configs;

        if (!isset($configs[$name])) {
            $class = self::$config_namespace . "\\{$name}";
            $file = str_replace('\\', '/', ROOT_PATH . '\..' . strtolower(self::$config_namespace) . "\\{$name}.json");
            if (empty($configs[$name]) && class_exists($class)) {
                $config = new $class();
                $configs[$name] = $config;
                $conf = $config->getConfig();
                if (!empty($conf) && !file_exists($file)) {
                    // file_put_contents($file, json_encode($conf));
                }
            } else if (file_exists($file)) {
                $configJson = file_get_contents($file);
                $config = json_decode($configJson, TRUE);
                if (!empty($config)) {
                    $configs[$name] = new self($config);
                }
            }
        }
        return $configs[$name];
    }

    final static public function func(string $key)
    {
        $func = static::config($key) ?? NULL;
        return $func;
    }

    static protected function config($func) {
        $func_config = [
        ];
        return $func_config[$func] ?? ($func_config[$func] ?? NULL);
    }

    /**
     * Set the namespace of all config classes.
     */
    public static function setConfigNamespace($namespace) {
        self::$config_namespace = $namespace;
        parent::setConfigNamespace($namespace);     
    }

    private static $config_namespace = NULL;

    ////////////////////////////////////////

    protected function __construct($config = NULL) {
        if (!empty($config)) {
            $this->config = $config;
        }
    }

    private $config;

    public function __get($name) {
        return $this->config[$name];
    }

    public function __set($name, $value) {
        $this->config[$name] = $value;
    }

    public function getConfig() {
        return $this->config;
    }
    /**
     * 获取同级子域名的方法,
     * @param string $subDomain
     * @return array|string
     */
    public function getHost($subDomain = 'www')
    {
        $host = $_SERVER['HTTP_HOST'];
        $host = explode('.', $host);
        $host[0] = $subDomain;
        $host = 'http://' . implode('.', $host) . '/';
        return $host;
    }

}
