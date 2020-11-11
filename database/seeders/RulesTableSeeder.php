<?php

namespace Database\Seeders;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rule_condition_type_groups')->insert([[
            'name' => 'Traffic Sources Fields',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Trackers Fields',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Other',
            'created_at' => Carbon::now()
        ]]);

        DB::table('rule_condition_types')->insert([[
            'name' => 'Impressions',
            'rule_condition_type_group_id' => 1,
            'provider' => 'Impressions',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source Clicks',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceClicks',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source Conversions',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceConversions',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source Revenue',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceRevenue',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source Spent',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceSpent',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source EPC',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceEPC',
            'created_at' => Carbon::now()
        ], [
            'name' => 'CTR',
            'rule_condition_type_group_id' => 1,
            'provider' => 'CTR',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source CVR',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceCVR',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source NET',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceNET',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source ROI',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceROI',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Traffic Source CPA',
            'rule_condition_type_group_id' => 1,
            'provider' => 'TrafficSourceCPA',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Avg. CPC',
            'rule_condition_type_group_id' => 1,
            'provider' => 'AvgCPC',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Estimated Spent',
            'rule_condition_type_group_id' => 1,
            'provider' => 'EstimatedSpent',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Estimated NET',
            'rule_condition_type_group_id' => 1,
            'provider' => 'EstimatedNET',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Estimated ROI',
            'rule_condition_type_group_id' => 1,
            'provider' => 'EstimatedROI',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker Clicks',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerClicks',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker Conversions',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerConversions',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker Revenue',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerRevenue',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Landing Page Clicks',
            'rule_condition_type_group_id' => 2,
            'provider' => 'LandingPageClicks',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker EPC',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerEPC',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Landing Page CTR',
            'rule_condition_type_group_id' => 2,
            'provider' => 'LandingPageCTR',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker CVR',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerCVR',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker NET',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerNET',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker ROI',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerROI',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Tracker CPA',
            'rule_condition_type_group_id' => 2,
            'provider' => 'TrackerCPA',
            'created_at' => Carbon::now()
        ], [
            'name' => 'EPC',
            'rule_condition_type_group_id' => 2,
            'provider' => 'EPC',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Hour Of Day',
            'rule_condition_type_group_id' => 3,
            'provider' => 'HourOfDay',
            'created_at' => Carbon::now()
        ]]);

        DB::table('rule_data_from_options')->insert([[
            'name' => 'Today',
            'excluded_day_id' => 1,
            'provider' => 'Today',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Yesterday',
            'excluded_day_id' => 1,
            'provider' => 'Yesterday',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Last 3 days',
            'excluded_day_id' => 3,
            'provider' => 'Last3days',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Last 7 days',
            'excluded_day_id' => 3,
            'provider' => 'Last7days',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Last 14 days',
            'excluded_day_id' => 3,
            'provider' => 'Last14days',
            'created_at' => Carbon::now()
        ], [
            'name' => 'This month',
            'excluded_day_id' => 3,
            'provider' => 'ThisMonth',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Last 30 days',
            'excluded_day_id' => 3,
            'provider' => 'Last30days',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Last 60 days',
            'excluded_day_id' => 3,
            'provider' => 'Last60days',
            'created_at' => Carbon::now()
        ], [
            'name' => 'Last 90 days',
            'excluded_day_id' => 3,
            'provider' => 'Last90days',
            'created_at' => Carbon::now()
        ]]);
    }
}
