<?php
namespace Air;

define('ROOT_PATH', __DIR__ . '/..');
define('LIB_PATH', ROOT_PATH . '/../phplib');
//internal lib
require_once(ROOT_PATH . '/libs/base/Autoloader.class.php');
require_once(ROOT_PATH . '/vendor/autoload.php');
require_once(ROOT_PATH . '/vendor/barcode_autoload.php');
if (file_exists('/etc/config.php')) {
    require_once('/etc/config.php');
}
//config
$env = 'config';
if (stripos('pre' . $_SERVER['HTTP_HOST'], 'yp') || stripos('pre' . $_SERVER['HTTP_HOST'], 'label')) {
    define('SESSION_ID', 'ophthalmology');
}
else {
    define('SESSION_ID', 'fantastic');
}
require_once(ROOT_PATH . '/config/' . $env . '/config.inc.php');

$root_path_setting = array(
    'air' => ROOT_PATH,
    'phplib' => LIB_PATH,
);

$autoloader = Libs\Base\Autoloader::get($root_path_setting);

\Air\Libs\Profiler::getProfiler()->startup();

\Phplib\Tools\Logger::setLogLevel(LOG_DEBUG);
// configuration of MySQL, Redis, Memcache, etc.
Libs\Base\Config::setConfigNamespace('\\Air\\Config\\' . ucfirst($env));

$locale = trim($_REQUEST['language']);
Libs\Base\Utilities::setI18n($locale);

$dispatcher = Libs\Dispatcher::get();
$dispatcher->dispatch();

//exec fastcgi_finish_request
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

if (ENV == 'test') Libs\DDDebuger::getDDDebuger()->sendMassage();

//final work
if (method_exists($dispatcher,'asyncJob')) {
    $dispatcher->asyncJob();
}

\Air\Libs\Profiler::getProfiler()->shutdown();