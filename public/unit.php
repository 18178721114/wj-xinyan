<?php
namespace Air;

if ($argc < 2) {
    exit("Wrong parameters");
}

define('ROOT_PATH', __DIR__ . '/..');
define('LIB_PATH', ROOT_PATH . '/../phplib');

//internal lib
require_once(ROOT_PATH . '/libs/base/Autoloader.class.php');
require_once(ROOT_PATH . '/vendor/autoload.php');
require_once(ROOT_PATH . '/vendor/barcode_autoload.php');

$env = 'config';
define('SESSION_ID', 'fantastic');
require_once(ROOT_PATH . '/config/' . $env . '/config.inc.php');
$root_path_setting = array(
    'air' => ROOT_PATH,
    'phplib' => LIB_PATH,
);

$autoloader = Libs\Base\Autoloader::get($root_path_setting);
\Phplib\Tools\Logger::setLogLevel(LOG_DEBUG);
// set config namespace for IO's config loader so IO can load proper
// configuration of MySQL, Redis, Memcache, etc.
Libs\Base\Config::setConfigNamespace('\\Air\\Config\\' . ucfirst($env));
$class = "\\Air\\Unit\\{$argv[1]}";
define('SCRIPT_CALL_NAME', $argv[1]);
if (!class_exists($class)) {
    exit("Wrong class");
}

ini_set('default_socket_timeout', -1);

//delete run.php from argv
array_shift($argv);
//delete the class name from argv
array_shift($argv);

$worker = new $class($argv);
$worker->run();
if ($worker->open_debuger && ENV == 'test') {
    Libs\DDDebuger::getDDDebuger()->setUserName('UnitTest');
    Libs\DDDebuger::getDDDebuger()->setAPI($class);
    Libs\DDDebuger::getDDDebuger()->sendMassage();
}
exit(0);
