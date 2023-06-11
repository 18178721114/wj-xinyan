<?php

namespace Air;
date_default_timezone_set("Asia/Shanghai");
define('ROOT_PATH', __DIR__ . '/..');
define('LIB_PATH', ROOT_PATH . '/../phplib');

//internal lib
require_once(ROOT_PATH . '/libs/base/Autoloader.class.php');
require_once(ROOT_PATH . '/vendor/autoload.php');
require_once(ROOT_PATH . '/vendor/barcode_autoload.php');
if (file_exists('/etc/config.php')) {
    require_once('/etc/config.php');
}
$env = 'config';
define('SESSION_ID', 'fantastic');
require_once(ROOT_PATH . '/config/' . $env . '/config.inc.php');
$root_path_setting = array(
    'air' => ROOT_PATH,
    'phplib' => LIB_PATH,
);
Libs\Base\Autoloader::get($root_path_setting);
\Phplib\Tools\Logger::setLogLevel(LOG_DEBUG);
// set config namespace for IO's config loader so IO can load proper
// configuration of MySQL, Redis, Memcache, etc.
Libs\Base\Config::setConfigNamespace('\\Air\\Config\\' . ucfirst($env));
// $locale = 'zh_CN';
$locale = '';
// $locale = Libs\Base\Utilities::getLocale($locale);
putenv('LANG=' . $locale);
