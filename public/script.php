<?php
namespace Air;

if ($argc < 2) {
    exit("Wrong parameters");
}

//处理命令参数
$new_argv = [];
foreach ($argv as $v) {
    if(substr($v, 0, 2) != '--') { //前面带--参数去掉，可以使用getopt函数获取
        $new_argv[] = $v;
    }
}
$argv = $new_argv;
$_SERVER['argv'] = $argv;

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
$autoloader = Libs\Base\Autoloader::get($root_path_setting);
\Phplib\Tools\Logger::setLogLevel(LOG_DEBUG);
// set config namespace for IO's config loader so IO can load proper
// configuration of MySQL, Redis, Memcache, etc.
Libs\Base\Config::setConfigNamespace('\\Air\\Config\\' . ucfirst($env));
// $locale = 'zh_CN';
$locale = '';
// $locale = Libs\Base\Utilities::getLocale($locale);
putenv('LANG=' . $locale);
// setlocale(LC_ALL, $locale . ".utf8");
// bindtextdomain('i18n', ROOT_PATH . '/resource/language');
// bind_textdomain_codeset('i18n', 'UTF-8');
// textdomain('i18n');
$class = "\\Air\\Scripts\\{$argv[1]}";
define('SCRIPT_CALL_NAME', $argv[1]);
if (!class_exists($class)) {
    exit("Wrong class");
}
ini_set('default_socket_timeout', -1);

//delete run.php from argv
array_shift($argv);
//delete the class name from argv
array_shift($argv);

register_shutdown_function(function() {
    script_shutdown();
});

\Air\Libs\Profiler::getProfiler()->startup();

$worker = new $class($argv);
$worker->run();
if ($worker->open_debuger && ENV == 'test') {
    Libs\DDDebuger::getDDDebuger()->setUserName('Script');
    Libs\DDDebuger::getDDDebuger()->setAPI($class);
    Libs\DDDebuger::getDDDebuger()->sendMassage();
}

//脚本结束时调用此方法，包括exit时
function script_shutdown() {
    \Air\Libs\Profiler::getProfiler()->shutdown();
    \Phplib\DB\Database::shutdown();
}

exit(0);
