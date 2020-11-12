<?php

namespace App\Utils\RuleConditionTypes;

use App\Utils\ReportData;

class EstimatedSpent extends Root
{
    public function check($performance_data, $rule_condition)
    {
        $sum_spends = ReportData::sum($performance_data, 'spend');

        return parent::compare($sum_spends, $rule_condition->amount, $rule_condition->operation);
    }
}
