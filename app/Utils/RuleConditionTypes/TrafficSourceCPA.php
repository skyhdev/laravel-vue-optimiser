<?php

namespace App\Utils\RuleConditionTypes;

use App\Utils\ReportData;

class TrafficSourceCPA extends Root
{
    public function check($campaign, $performance_data, $rule_condition, $calculation_type)
    {
        $spends = ReportData::sum($campaign, $performance_data, 'spend', $calculation_type);
        $conversions = ReportData::sum($campaign, $performance_data, 'conversions', $calculation_type);

        return parent::compare($conversions ? $spends / $conversions : INF, $rule_condition->amount, $rule_condition->operation);
    }
}
