<?php

namespace App\Utils\RuleConditionTypes;

use App\Utils\ReportData;

class AvgCPC extends Root
{
    public function check($performance_data, $rule_condition)
    {
        $sum_spends = ReportData::sum($performance_data, 'spend');
        $sum_clicks = ReportData::sum($performance_data, 'click');

        return parent::compare($sum_spends / $sum_clicks, $rule_condition->amount, $rule_condition->operation);
    }
}
