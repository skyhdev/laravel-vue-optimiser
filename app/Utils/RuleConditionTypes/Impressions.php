<?php

namespace App\Utils\RuleConditionTypes;

use App\Utils\ReportData;

class Impressions extends Root
{
    public function check($campaign, $performance_data, $rule_condition)
    {
        $sum_impressions = ReportData::sum($campaign, $performance_data, 'impressions');

        return parent::compare($sum_impressions, $rule_condition->amount, $rule_condition->operation);
    }
}
