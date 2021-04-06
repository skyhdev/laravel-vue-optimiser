<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTableIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function($table) {
            $table->index('campaign_id', 'campaigns_campaign_id__index');
            $table->index('user_id', 'campaigns_user_id__index');
            $table->index('provider_id', 'campaigns_provider_id__index');
            $table->index('open_id', 'campaigns_open_id__index');
        });

        Schema::table('ad_groups', function($table) {
            $table->index('ad_group_id', 'ad_groups_ad_group_id__index');
            $table->index('user_id', 'ad_groups_user_id__index');
            $table->index('provider_id', 'ad_groups_provider_id__index');
            $table->index('open_id', 'ad_groups_open_id__index');
            $table->index('campaign_id', 'ad_groups_campaign_id__index');
            $table->index('advertiser_id', 'ad_groups_advertiser_id__index');
        });

        Schema::table('ads', function($table) {
            $table->index('ad_id', 'ads_ad_id__index');
            $table->index('user_id', 'ads_user_id__index');
            $table->index('provider_id', 'ads_provider_id__index');
            $table->index('open_id', 'ads_open_id__index');
            $table->index('campaign_id', 'ads_campaign_id__index');
            $table->index('ad_group_id', 'ads_ad_group_id__index');
            $table->index('advertiser_id', 'ads_advertiser_id__index');
        });

        Schema::table('gemini_ad_extension_details', function($table) {
            $table->index('advertiser_id', 'gemini_ad_extension_details_advertiser_id__index');
            $table->index('campaign_id', 'gemini_ad_extension_details_campaign_id__index');
            $table->index('ad_group_id', 'gemini_ad_extension_details_ad_group_id__index');
            $table->index('ad_id', 'gemini_ad_extension_details_ad_id__index');
            $table->index('keyword_id', 'gemini_ad_extension_details_keyword_id__index');
            $table->index('ad_extn_id', 'gemini_ad_extension_details_ad_extn_id__index');
            $table->index('device_type', 'gemini_ad_extension_details_device_type__index');
            $table->index('month', 'gemini_ad_extension_details_month__index');
            $table->index('week', 'gemini_ad_extension_details_week__index');
            $table->index('day', 'gemini_ad_extension_details_day__index');
            $table->index('pricing_type', 'gemini_ad_extension_details_pricing_type__index');
        });

        Schema::table('gemini_adjustment_stats', function($table) {
            $table->index('advertiser_id', 'gemini_adjustment_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_adjustment_stats_campaign_id__index');
            $table->index('pricing_type', 'gemini_adjustment_stats_pricing_type__index');
            $table->index('source_name', 'gemini_adjustment_stats_source_name__index');
            $table->index('is_adjustment', 'gemini_adjustment_stats_is_adjustment__index');
            $table->index('adjustment_type', 'gemini_adjustment_stats_adjustment_type__index');
            $table->index('day', 'gemini_adjustment_stats_day__index');
        });

        Schema::table('gemini_call_extension_stats', function($table) {
            $table->index('advertiser_id', 'gemini_call_extension_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_call_extension_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_call_extension_stats_ad_group_id__index');
            $table->index('month', 'gemini_call_extension_stats_month__index');
            $table->index('week', 'gemini_call_extension_stats_week__index');
            $table->index('day', 'gemini_call_extension_stats_day__index');
        });

        Schema::table('gemini_campaign_bid_performance_stats', function($table) {
            $table->index('advertiser_id', 'gemini_campaign_bid_performance_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_campaign_bid_performance_stats_campaign_id__index');
            $table->index('section_id', 'gemini_campaign_bid_performance_stats_section_id__index');
            $table->index('ad_group_id', 'gemini_campaign_bid_performance_stats_ad_group_id__index');
            $table->index('day', 'gemini_campaign_bid_performance_stats_day__index');
            $table->index('supply_type', 'gemini_campaign_bid_performance_stats_supply_type__index');
        });

        Schema::table('gemini_conversion_rules_stats', function($table) {
            $table->index('advertiser_id', 'gemini_conversion_rules_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_conversion_rules_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_conversion_rules_stats_ad_group_id__index');
            $table->index('rule_id', 'gemini_conversion_rules_stats_rule_id__index');
            $table->index('keyword_id', 'gemini_conversion_rules_stats_keyword_id__index');
            $table->index('price_type', 'gemini_conversion_rules_stats_price_type__index');
            $table->index('day', 'gemini_conversion_rules_stats_day__index');
        });

        Schema::table('gemini_domain_performance_stats', function($table) {
            $table->index('advertiser_id', 'gemini_domain_performance_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_domain_performance_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_domain_performance_stats_ad_group_id__index');
            $table->index('day', 'gemini_domain_performance_stats_day__index');
        });

        Schema::table('gemini_jobs', function($table) {
            $table->index('user_id', 'gemini_jobs_user_id__index');
            $table->index('campaign_id', 'gemini_jobs_campaign_id__index');
            $table->index('advertiser_id', 'gemini_jobs_advertiser_id__index');
            $table->index('job_id', 'gemini_jobs_job_id__index');
            $table->index('status', 'gemini_jobs_status__index');
        });

        Schema::table('gemini_keyword_stats', function($table) {
            $table->index('advertiser_id', 'gemini_keyword_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_keyword_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_keyword_stats_ad_group_id__index');
            $table->index('ad_id', 'gemini_keyword_stats_ad_id__index');
            $table->index('keyword_id', 'gemini_keyword_stats_keyword_id__index');
            $table->index('day', 'gemini_keyword_stats_day__index');
            $table->index('device_type', 'gemini_keyword_stats_device_type__index');
        });

        Schema::table('gemini_performance_stats', function($table) {
            $table->index('advertiser_id', 'gemini_performance_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_performance_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_performance_stats_ad_group_id__index');
            $table->index('ad_id', 'gemini_performance_stats_ad_id__index');
            $table->index('month', 'gemini_performance_stats_month__index');
            $table->index('week', 'gemini_performance_stats_week__index');
            $table->index('day', 'gemini_performance_stats_day__index');
            $table->index('hour', 'gemini_performance_stats_hour__index');
            $table->index('pricing_type', 'gemini_performance_stats_pricing_type__index');
            $table->index('device_type', 'gemini_performance_stats_device_type__index');
            $table->index('source_name', 'gemini_performance_stats_source_name__index');
        });

        Schema::table('gemini_product_ad_performance_stats', function($table) {
            $table->index('advertiser_id', 'gemini_product_ad_performance_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_product_ad_performance_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_product_ad_performance_stats_ad_group_id__index');
            $table->index('product_ad_id', 'gemini_product_ad_performance_stats_product_ad_id__index');
            $table->index('month', 'gemini_product_ad_performance_stats_month__index');
            $table->index('week', 'gemini_product_ad_performance_stats_week__index');
            $table->index('day', 'gemini_product_ad_performance_stats_day__index');
            $table->index('hour', 'gemini_product_ad_performance_stats_hour__index');
            $table->index('pricing_type', 'gemini_product_ad_performance_stats_pricing_type__index');
            $table->index('device_type', 'gemini_product_ad_performance_stats_device_type__index');
            $table->index('source_name', 'gemini_product_ad_performance_stats_source_name__index');
        });

        Schema::table('gemini_product_ads', function($table) {
            $table->index('advertiser_id', 'gemini_product_ads_advertiser_id__index');
            $table->index('campaign_id', 'gemini_product_ads_campaign_id__index');
            $table->index('ad_group_id', 'gemini_product_ads_ad_group_id__index');
            $table->index('offer_id', 'gemini_product_ads_offer_id__index');
            $table->index('category_id', 'gemini_product_ads_category_id__index');
            $table->index('category_name', 'gemini_product_ads_category_name__index');
            $table->index('device', 'gemini_product_ads_device__index');
            $table->index('product_type', 'gemini_product_ads_product_type__index');
            $table->index('brand', 'gemini_product_ads_brand__index');
            $table->index('offer_group_id', 'gemini_product_ads_offer_group_id__index');
            $table->index('product_id', 'gemini_product_ads_product_id__index');
            $table->index('product_name', 'gemini_product_ads_product_name__index');
            $table->index('custom_label_0', 'gemini_product_ads_custom_label_0__index');
            $table->index('custom_label_1', 'gemini_product_ads_custom_label_1__index');
            $table->index('custom_label_2', 'gemini_product_ads_custom_label_2__index');
            $table->index('custom_label_3', 'gemini_product_ads_custom_label_3__index');
            $table->index('custom_label_4', 'gemini_product_ads_custom_label_4__index');
            $table->index('source', 'gemini_product_ads_source__index');
            $table->index('device_type', 'gemini_product_ads_device_type__index');
            $table->index('month', 'gemini_product_ads_month__index');
            $table->index('week', 'gemini_product_ads_week__index');
            $table->index('day', 'gemini_product_ads_day__index');
        });

        Schema::table('gemini_search_stats', function($table) {
            $table->index('advertiser_id', 'gemini_search_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_search_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_search_stats_ad_group_id__index');
            $table->index('ad_id', 'gemini_search_stats_ad_id__index');
            $table->index('keyword_id', 'gemini_search_stats_keyword_id__index');
            $table->index('delivered_match_type', 'gemini_search_stats_delivered_match_type__index');
            $table->index('search_term', 'gemini_search_stats_search_term__index');
            $table->index('device_type', 'gemini_search_stats_device_type__index');
            $table->index('day', 'gemini_search_stats_day__index');
        });

        Schema::table('gemini_site_performance_stats', function($table) {
            $table->index('advertiser_id', 'gemini_site_performance_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_site_performance_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_site_performance_stats_ad_group_id__index');
            $table->index('day', 'gemini_site_performance_stats_day__index');
            $table->index('device_type', 'gemini_site_performance_stats_device_type__index');
        });

        Schema::table('gemini_slot_performance_stats', function($table) {
            $table->index('advertiser_id', 'gemini_slot_performance_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_slot_performance_stats_campaign_id__index');
            $table->index('ad_group_id', 'gemini_slot_performance_stats_ad_group_id__index');
            $table->index('ad_id', 'gemini_slot_performance_stats_ad_id__index');
            $table->index('month', 'gemini_slot_performance_stats_month__index');
            $table->index('week', 'gemini_slot_performance_stats_week__index');
            $table->index('day', 'gemini_slot_performance_stats_day__index');
            $table->index('hour', 'gemini_slot_performance_stats_hour__index');
            $table->index('pricing_type', 'gemini_slot_performance_stats_pricing_type__index');
            $table->index('source', 'gemini_slot_performance_stats_source__index');
            $table->index('card_id', 'gemini_slot_performance_stats_card_id__index');
            $table->index('card_position', 'gemini_slot_performance_stats_card_position__index');
            $table->index('rendered_type', 'gemini_slot_performance_stats_rendered_type__index');
        });

        Schema::table('gemini_structured_snippet_extension_stats', function($table) {
            $table->index('advertiser_id', 'g_s_s_e_stats_advertiser_id__index');
            $table->index('campaign_id', 'g_s_s_e_stats_campaign_id__index');
            $table->index('ad_group_id', 'g_s_s_e_stats_ad_group_id__index');
            $table->index('ad_id', 'g_s_s_e_stats_ad_id__index');
            $table->index('month', 'g_s_s_e_stats_month__index');
            $table->index('week', 'g_s_s_e_stats_week__index');
            $table->index('day', 'g_s_s_e_stats_day__index');
            $table->index('keyword_id', 'g_s_s_e_stats_keyword_id__index');
            $table->index('structured_snippet_extn_id', 'g_s_s_e_stats_structured_snippet_extn_id__index');
            $table->index('pricing_type', 'g_s_s_e_stats_pricing_type__index');
            $table->index('source', 'g_s_s_e_stats_source__index');
        });

        Schema::table('gemini_user_stats', function($table) {
            $table->index('advertiser_id', 'gemini_user_stats_advertiser_id__index');
            $table->index('campaign_id', 'gemini_user_stats_campaign_id__index');
            $table->index('audience_id', 'gemini_user_stats_audience_id__index');
            $table->index('audience_name', 'gemini_user_stats_audience_name__index');
            $table->index('audience_type', 'gemini_user_stats_audience_type__index');
            $table->index('audience_status', 'gemini_user_stats_audience_status__index');
            $table->index('ad_group_id', 'gemini_user_stats_ad_group_id__index');
            $table->index('day', 'gemini_user_stats_day__index');
            $table->index('pricing_type', 'gemini_user_stats_pricing_type__index');
            $table->index('source_name', 'gemini_user_stats_source_name__index');
            $table->index('gender', 'gemini_user_stats_gender__index');
            $table->index('device_type', 'gemini_user_stats_device_type__index');
            $table->index('country', 'gemini_user_stats_country__index');
            $table->index('state', 'gemini_user_stats_state__index');
            $table->index('city', 'gemini_user_stats_city__index');
            $table->index('zip', 'gemini_user_stats_zip__index');
            $table->index('dma_woeid', 'gemini_user_stats_dma_woeid__index');
            $table->index('city_woeid', 'gemini_user_stats_city_woeid__index');
            $table->index('state_woeid', 'gemini_user_stats_state_woeid__index');
            $table->index('zip_woeid', 'gemini_user_stats_zip_woeid__index');
            $table->index('country_woeid', 'gemini_user_stats_country_woeid__index');
            $table->index('location_type', 'gemini_user_stats_location_type__index');
        });

        Schema::table('image_tags', function($table) {
            $table->index('image_id', 'image_tags_image_id__index');
            $table->index('tag_id', 'image_tags_tag_id__index');
        });

        Schema::table('job_batches', function($table) {
            $table->index('name', 'job_batches_name__index');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function($table) {
            $table->dropIndex('campaigns_campaign_id__index');
            $table->dropIndex('campaigns_user_id__index');
            $table->dropIndex('campaigns_provider_id__index');
            $table->dropIndex('campaigns_open_id__index');
        });

        Schema::table('ad_groups', function($table) {
            $table->dropIndex('ad_groups_ad_group_id__index');
            $table->dropIndex('ad_groups_user_id__index');
            $table->dropIndex('ad_groups_provider_id__index');
            $table->dropIndex('ad_groups_open_id__index');
            $table->dropIndex('ad_groups_campaign_id__index');
            $table->dropIndex('ad_groups_advertiser_id__index');
        });

        Schema::table('ads', function($table) {
            $table->dropIndex('ads_ad_id__index');
            $table->dropIndex('ads_user_id__index');
            $table->dropIndex('ads_provider_id__index');
            $table->dropIndex('ads_open_id__index');
            $table->dropIndex('ads_campaign_id__index');
            $table->dropIndex('ads_ad_group_id__index');
            $table->dropIndex('ads_advertiser_id__index');
        });

        Schema::table('gemini_ad_extension_details', function($table) {
            $table->dropIndex('gemini_ad_extension_details_advertiser_id__index');
            $table->dropIndex('gemini_ad_extension_details_campaign_id__index');
            $table->dropIndex('gemini_ad_extension_details_ad_group_id__index');
            $table->dropIndex('gemini_ad_extension_details_ad_id__index');
            $table->dropIndex('gemini_ad_extension_details_keyword_id__index');
            $table->dropIndex('gemini_ad_extension_details_ad_extn_id__index');
            $table->dropIndex('gemini_ad_extension_details_device_type__index');
            $table->dropIndex('gemini_ad_extension_details_month__index');
            $table->dropIndex('gemini_ad_extension_details_week__index');
            $table->dropIndex('gemini_ad_extension_details_day__index');
            $table->dropIndex('gemini_ad_extension_details_pricing_type__index');
        });

        Schema::table('gemini_adjustment_stats', function($table) {
            $table->dropIndex('gemini_adjustment_stats_advertiser_id__index');
            $table->dropIndex('gemini_adjustment_stats_campaign_id__index');
            $table->dropIndex('gemini_adjustment_stats_pricing_type__index');
            $table->dropIndex('gemini_adjustment_stats_source_name__index');
            $table->dropIndex('gemini_adjustment_stats_is_adjustment__index');
            $table->dropIndex('gemini_adjustment_stats_adjustment_type__index');
            $table->dropIndex('gemini_adjustment_stats_day__index');
        });

        Schema::table('gemini_call_extension_stats', function($table) {
            $table->dropIndex('gemini_call_extension_stats_advertiser_id__index');
            $table->dropIndex('gemini_call_extension_stats_campaign_id__index');
            $table->dropIndex('gemini_call_extension_stats_ad_group_id__index');
            $table->dropIndex('gemini_call_extension_stats_month__index');
            $table->dropIndex('gemini_call_extension_stats_week__index');
            $table->dropIndex('gemini_call_extension_stats_day__index');
        });

        Schema::table('gemini_campaign_bid_performance_stats', function($table) {
            $table->dropIndex('gemini_campaign_bid_performance_stats_advertiser_id__index');
            $table->dropIndex('gemini_campaign_bid_performance_stats_campaign_id__index');
            $table->dropIndex('gemini_campaign_bid_performance_stats_section_id__index');
            $table->dropIndex('gemini_campaign_bid_performance_stats_ad_group_id__index');
            $table->dropIndex('gemini_campaign_bid_performance_stats_day__index');
            $table->dropIndex('gemini_campaign_bid_performance_stats_supply_type__index');
        });

        Schema::table('gemini_conversion_rules_stats', function($table) {
            $table->dropIndex('gemini_conversion_rules_stats_advertiser_id__index');
            $table->dropIndex('gemini_conversion_rules_stats_campaign_id__index');
            $table->dropIndex('gemini_conversion_rules_stats_ad_group_id__index');
            $table->dropIndex('gemini_conversion_rules_stats_rule_id__index');
            $table->dropIndex('gemini_conversion_rules_stats_keyword_id__index');
            $table->dropIndex('gemini_conversion_rules_stats_price_type__index');
            $table->dropIndex('gemini_conversion_rules_stats_day__index');
        });

        Schema::table('gemini_domain_performance_stats', function($table) {
            $table->dropIndex('gemini_domain_performance_stats_advertiser_id__index');
            $table->dropIndex('gemini_domain_performance_stats_campaign_id__index');
            $table->dropIndex('gemini_domain_performance_stats_ad_group_id__index');
            $table->dropIndex('gemini_domain_performance_stats_day__index');
        });

        Schema::table('gemini_jobs', function($table) {
            $table->dropIndex('gemini_jobs_user_id__index');
            $table->dropIndex('gemini_jobs_campaign_id__index');
            $table->dropIndex('gemini_jobs_advertiser_id__index');
            $table->dropIndex('gemini_jobs_job_id__index');
            $table->dropIndex('gemini_jobs_status__index');
        });

        Schema::table('gemini_keyword_stats', function($table) {
            $table->dropIndex('gemini_keyword_stats_advertiser_id__index');
            $table->dropIndex('gemini_keyword_stats_campaign_id__index');
            $table->dropIndex('gemini_keyword_stats_ad_group_id__index');
            $table->dropIndex('gemini_keyword_stats_ad_id__index');
            $table->dropIndex('gemini_keyword_stats_keyword_id__index');
            $table->dropIndex('gemini_keyword_stats_day__index');
            $table->dropIndex('gemini_keyword_stats_device_type__index');
        });

        Schema::table('gemini_performance_stats', function($table) {
            $table->dropIndex('gemini_performance_stats_advertiser_id__index');
            $table->dropIndex('gemini_performance_stats_campaign_id__index');
            $table->dropIndex('gemini_performance_stats_ad_group_id__index');
            $table->dropIndex('gemini_performance_stats_ad_id__index');
            $table->dropIndex('gemini_performance_stats_month__index');
            $table->dropIndex('gemini_performance_stats_week__index');
            $table->dropIndex('gemini_performance_stats_day__index');
            $table->dropIndex('gemini_performance_stats_hour__index');
            $table->dropIndex('gemini_performance_stats_pricing_type__index');
            $table->dropIndex('gemini_performance_stats_device_type__index');
            $table->dropIndex('gemini_performance_stats_source_name__index');
        });

        Schema::table('gemini_product_ad_performance_stats', function($table) {
            $table->dropIndex('gemini_product_ad_performance_stats_advertiser_id__index');
            $table->dropIndex('gemini_product_ad_performance_stats_campaign_id__index');
            $table->dropIndex('gemini_product_ad_performance_stats_ad_group_id__index');
            $table->dropIndex('gemini_product_ad_performance_stats_product_ad_id__index');
            $table->dropIndex('gemini_product_ad_performance_stats_month__index');
            $table->dropIndex('gemini_product_ad_performance_stats_week__index');
            $table->dropIndex('gemini_product_ad_performance_stats_day__index');
            $table->dropIndex('gemini_product_ad_performance_stats_hour__index');
            $table->dropIndex('gemini_product_ad_performance_stats_pricing_type__index');
            $table->dropIndex('gemini_product_ad_performance_stats_device_type__index');
            $table->dropIndex('gemini_product_ad_performance_stats_source_name__index');
        });

        Schema::table('gemini_product_ads', function($table) {
            $table->dropIndex('gemini_product_ads_advertiser_id__index');
            $table->dropIndex('gemini_product_ads_campaign_id__index');
            $table->dropIndex('gemini_product_ads_ad_group_id__index');
            $table->dropIndex('gemini_product_ads_offer_id__index');
            $table->dropIndex('gemini_product_ads_category_id__index');
            $table->dropIndex('gemini_product_ads_category_name__index');
            $table->dropIndex('gemini_product_ads_device__index');
            $table->dropIndex('gemini_product_ads_product_type__index');
            $table->dropIndex('gemini_product_ads_brand__index');
            $table->dropIndex('gemini_product_ads_offer_group_id__index');
            $table->dropIndex('gemini_product_ads_product_id__index');
            $table->dropIndex('gemini_product_ads_product_name__index');
            $table->dropIndex('gemini_product_ads_custom_label_0__index');
            $table->dropIndex('gemini_product_ads_custom_label_1__index');
            $table->dropIndex('gemini_product_ads_custom_label_2__index');
            $table->dropIndex('gemini_product_ads_custom_label_3__index');
            $table->dropIndex('gemini_product_ads_custom_label_4__index');
            $table->dropIndex('gemini_product_ads_source__index');
            $table->dropIndex('gemini_product_ads_device_type__index');
            $table->dropIndex('gemini_product_ads_month__index');
            $table->dropIndex('gemini_product_ads_week__index');
            $table->dropIndex('gemini_product_ads_day__index');
        });

        Schema::table('gemini_search_stats', function($table) {
            $table->dropIndex('gemini_search_stats_advertiser_id__index');
            $table->dropIndex('gemini_search_stats_campaign_id__index');
            $table->dropIndex('gemini_search_stats_ad_group_id__index');
            $table->dropIndex('gemini_search_stats_ad_id__index');
            $table->dropIndex('gemini_search_stats_keyword_id__index');
            $table->dropIndex('gemini_search_stats_delivered_match_type__index');
            $table->dropIndex('gemini_search_stats_search_term__index');
            $table->dropIndex('gemini_search_stats_device_type__index');
            $table->dropIndex('gemini_search_stats_day__index');
        });

        Schema::table('gemini_site_performance_stats', function($table) {
            $table->dropIndex('gemini_site_performance_stats_advertiser_id__index');
            $table->dropIndex('gemini_site_performance_stats_campaign_id__index');
            $table->dropIndex('gemini_site_performance_stats_ad_group_id__index');
            $table->dropIndex('gemini_site_performance_stats_day__index');
            $table->dropIndex('gemini_site_performance_stats_device_type__index');
        });

        Schema::table('gemini_slot_performance_stats', function($table) {
            $table->dropIndex('gemini_slot_performance_stats_advertiser_id__index');
            $table->dropIndex('gemini_slot_performance_stats_campaign_id__index');
            $table->dropIndex('gemini_slot_performance_stats_ad_group_id__index');
            $table->dropIndex('gemini_slot_performance_stats_ad_id__index');
            $table->dropIndex('gemini_slot_performance_stats_month__index');
            $table->dropIndex('gemini_slot_performance_stats_week__index');
            $table->dropIndex('gemini_slot_performance_stats_day__index');
            $table->dropIndex('gemini_slot_performance_stats_hour__index');
            $table->dropIndex('gemini_slot_performance_stats_pricing_type__index');
            $table->dropIndex('gemini_slot_performance_stats_source__index');
            $table->dropIndex('gemini_slot_performance_stats_card_id__index');
            $table->dropIndex('gemini_slot_performance_stats_card_position__index');
            $table->dropIndex('gemini_slot_performance_stats_rendered_type__index');
        });

        Schema::table('gemini_structured_snippet_extension_stats', function($table) {
            $table->dropIndex('g_s_s_e_stats_advertiser_id__index');
            $table->dropIndex('g_s_s_e_stats_campaign_id__index');
            $table->dropIndex('g_s_s_e_stats_ad_group_id__index');
            $table->dropIndex('g_s_s_e_stats_ad_id__index');
            $table->dropIndex('g_s_s_e_stats_month__index');
            $table->dropIndex('g_s_s_e_stats_week__index');
            $table->dropIndex('g_s_s_e_stats_day__index');
            $table->dropIndex('g_s_s_e_stats_keyword_id__index');
            $table->dropIndex('g_s_s_e_stats_structured_snippet_extn_id__index');
            $table->dropIndex('g_s_s_e_stats_pricing_type__index');
            $table->dropIndex('g_s_s_e_stats_source__index');
        });

        Schema::table('gemini_user_stats', function($table) {
            $table->dropIndex('gemini_user_stats_advertiser_id__index');
            $table->dropIndex('gemini_user_stats_campaign_id__index');
            $table->dropIndex('gemini_user_stats_audience_id__index');
            $table->dropIndex('gemini_user_stats_audience_name__index');
            $table->dropIndex('gemini_user_stats_audience_type__index');
            $table->dropIndex('gemini_user_stats_audience_status__index');
            $table->dropIndex('gemini_user_stats_ad_group_id__index');
            $table->dropIndex('gemini_user_stats_day__index');
            $table->dropIndex('gemini_user_stats_pricing_type__index');
            $table->dropIndex('gemini_user_stats_source_name__index');
            $table->dropIndex('gemini_user_stats_gender__index');
            $table->dropIndex('gemini_user_stats_device_type__index');
            $table->dropIndex('gemini_user_stats_country__index');
            $table->dropIndex('gemini_user_stats_state__index');
            $table->dropIndex('gemini_user_stats_city__index');
            $table->dropIndex('gemini_user_stats_zip__index');
            $table->dropIndex('gemini_user_stats_dma_woeid__index');
            $table->dropIndex('gemini_user_stats_city_woeid__index');
            $table->dropIndex('gemini_user_stats_state_woeid__index');
            $table->dropIndex('gemini_user_stats_zip_woeid__index');
            $table->dropIndex('gemini_user_stats_country_woeid__index');
            $table->dropIndex('gemini_user_stats_location_type__index');
        });
    }
}
