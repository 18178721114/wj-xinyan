<?php
namespace Air\Unit\Float_risk;

use Air\Package\Checklist\DiseaseRisky;

class floatRisk 
{
    public function __construct($arg)
    {
        $this->args = $arg;
        $this->case_id = $arg[0];
        $this->data_id = $arg[1];
        $this->case_func = 'testCase' . $this->case_id;
    }
    const DATAS = [
        '1' => [
          //当前风险，历史风险，浮动范围，跨级
            [49.65, 49.65, 0.10, 1],
            [49.45, 49.45, 0.10, 1],
            [49.65, 49.45, 0.10, 1],
            [49.45, 49.65, 0.10, 1],
            [49.45, 50.45, 0.10, 1],
            [49.65, 50.45, 0.10, 1],
            [100, 100, 0.10, 1],
        ],
    ];

    

    public function run()
    {
        $result = [];
        $case_func = $this->case_func;
        foreach (self::DATAS[$this->data_id] as $case) {
            $result = $this->$case_func($case);
            echo $result . "\n";
        }
        echo "Bye\n";
    }


    public function testCase1($case) {
        list($risk, $old_risk, $float, $limit_level) = $case;
        $old_risk = round($old_risk);
        $risk = round(DiseaseRisky::floatRisk($risk, $old_risk, $float, $limit_level));
        $old_level = DiseaseRisky::getRiskLevel($old_risk);
        $risk_level = DiseaseRisky::getRiskLevel($risk);
        // 当前风险展示值，旧风险展示值，当前风险等级，旧风险等级
        return $risk . '-' . $old_risk . '-' . $risk_level . '-' . $old_level;
    }

}