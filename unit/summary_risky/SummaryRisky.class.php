<?php
namespace Air\Unit\Summary_risky;

use \Air\Package\Patient\Patient;
use Air\Package\Checklist\IcvdReport;

/**
 * php public/script.php ImportPatientNameFromCSV
 */
class SummaryRisky {
    public function __construct($arg) {
        $this->args = $arg;
    }
    const TO_SEND = ['wengjing@airdoc.com', 'hailong@airdoc.com'];
    public static $file = 'risky.csv';
    public function run() {
        $p_obj = new Patient;
        set_time_limit(0);
        $handle = fopen(ROOT_PATH . '/scripts/' . self::$file, 'r');
        if ($handle) {
            $pic_array = [];
            while  ($buffer = fgets($handle)) {
                $line = explode(",", $buffer);
                $risky = [
                    ['risky' => $line[0]],
                    ['risky' => $line[1]],
                    ['risky' => $line[2]],
                ];
                list($summary, $last_risky) = IcvdReport::getSummaryRisky($risky, $line[4], $line[3], 999, 1);
                echo trim($buffer) . ',' . $summary . ',' . $last_risky . "\n";
            }
        }
    }
}
