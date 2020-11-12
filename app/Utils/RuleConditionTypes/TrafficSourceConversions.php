<?php

namespace App\Utils\RuleConditionTypes;

use App\Utils\ReportData;

class TrafficSourceConversions extends Root
{
    public function check($performance_data, $rule_condition)
    {
        $sum_conversions = ReportData::sum($performance_data, 'conversions');

        return parent::compare($sum_conversions, $rule_condition->amount, $rule_condition->operation);
    }
}
