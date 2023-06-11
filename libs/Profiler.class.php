<?php
namespace Air\Libs;

use Air\Libs\Base\Utilities;

define('XHPROF_ROOT', ROOT_PATH . '/../xhprof');

class Profiler
{
    private static $instance = NULL;
    private $debug_status = FALSE;
    private $debug_name = '';

    public static function getProfiler()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->debug_status = FALSE; //默认关闭
        $this->debug_name = 'xhprof';
    }

    public function startup()
    {
        if (defined('SCRIPT_CALL_NAME')) {
            $debug_params = getopt('', ['debug::', 'debug_name::', 'debug_io::']);
            $debug = $debug_params['debug'] ?? '';
            $debug_name = !empty($debug_params['debug_name']) ? $debug_params['debug_name'] : SCRIPT_CALL_NAME;
            $debug_io = !empty($debug_params['debug_io']) ? $debug_params['debug_io'] : 0;
        } else {
            $debug = $_GET['debug'] ?? '';
            $debug_name = $_GET['debug_name'] ?? '';
            $debug_io = $_GET['debug_io'] ?? 0;
        }

        if (ENV == 'test' && $debug == date('md') . '25' && function_exists('xhprof_enable')) {
            $this->debug_status = TRUE;
            if ($debug_name) {
                $this->debug_name = trim($debug_name);
            }
            if ($debug_io == 1) { //开启收集mysql_query,curl_exec内部信息
                ini_set('xhprof.collect_additional_info', 1);
                xhprof_enable(XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
            } else {
                ini_set('xhprof.collect_additional_info', 0);
                xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
            }
        }
    }

    public function shutdown()
    {
        if (!$this->debug_status) {
            return FALSE;
        }

        $xhprof_data = xhprof_disable();
        include_once XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php';
        include_once XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php';
        $xhprof_runs = new \XHProfRuns_Default();
        $run_id = $xhprof_runs->save_run($xhprof_data, $this->debug_name);

        $url = sprintf(EYE_DOMAIN . 'xhprof_html/index.php?run=%s&source=%s', $run_id, $this->debug_name);
        Utilities::DDMonitor('Generate xhprof ' . $url . ' ', 'debuger');
    }
}
