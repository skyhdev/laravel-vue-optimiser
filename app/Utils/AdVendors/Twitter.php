<?php

namespace App\Utils\AdVendors;

use App\Endpoints\TwitterAPI;
use App\Jobs\DeleteAdGroup;
use App\Jobs\DeleteCampaign;
use App\Jobs\DeleteCard;
use App\Jobs\PullCampaign;
use App\Models\Ad;
use App\Models\AdGroup;
use App\Models\Campaign;
use App\Models\Provider;
use App\Models\RedtrackReport;
use App\Models\TwitterReport;
use App\Models\User;
use App\Models\UserTracker;
use App\Vngodev\AdVendorInterface;
use App\Vngodev\Helper;
use Carbon\Carbon;
use DB;
use Exception;
use GuzzleHttp\Client;
use Hborras\TwitterAdsSDK\TwitterAdsException;
use Illuminate\Support\Str;
use Log;

class Twitter extends Root implements AdVendorInterface
{
    private function api()
    {
        $provider = Provider::where('slug', request('provider'))->first();

        return new TwitterAPI(auth()->user()->providers()->where('provider_id', $provider->id)->where('open_id', request('account'))->first(), request('advertiser') ?? null);
    }

    public function advertisers()
    {
        $advertisers = $this->api()->getAdvertisers();

        $result = [];

        foreach ($advertisers as $advertiser) {
            $result[] = [
                'id' => $advertiser->getId(),
                'name' => $advertiser->getName()
            ];
        }

        return $result;
    }

    public function countries()
    {
        return $this->api()->getCountries();
    }

    public function getCampaignInstance(Campaign $campaign)
    {
        try {
            $api = new TwitterAPI(auth()->user()->providers()->where('provider_id', $campaign->provider_id)->where('open_id', $campaign->open_id)->first(), $campaign->advertiser_id);

            $instance = $api->getCampaign($campaign->campaign_id)->toArray();

            $instance['provider'] = $campaign->provider->slug;
            $instance['open_id'] = $campaign['open_id'];
            $instance['advertiser_id'] = $campaign->advertiser_id;
            $instance['instance_id'] = $campaign['id'];

            $instance['adGroups'] = [];

            $instance['ads'] = [];

            $ad_groups = $api->getAdGroups($campaign->campaign_id);

            if ($ad_groups && count($ad_groups) > 0) {
                foreach ($ad_groups as $ad_group) {
                    $instance['adGroups'][] = $ad_group->toArray();
                }

                $promoted_tweets = $api->getPromotedTweets([$ad_groups[0]->getId()]);

                if ($promoted_tweets && count($promoted_tweets) > 0) {
                    $tweets = $api->getTweet($promoted_tweets[0]->getTweetId());

                    $instance['promoted_tweet_id'] = $promoted_tweets[0]->getId();

                    if ($tweets && count($tweets) > 0) {
                        foreach ($tweets as $tweet) {
                            $instance['ads'][] = $tweet->toArray();
                        }
                    }
                }
            }

            return $instance;
        } catch (Exception $e) {
            return [];
        }
    }

    public function cloneCampaignName(&$instance)
    {
        $instance['name'] = $instance['name'] . ' - Copy';
    }

    public function fundingInstruments()
    {
        $funding_instruments = $this->api()->getFundingInstruments();

        $result = [];

        foreach ($funding_instruments as $funding_instrument) {
            $result[] = [
                'id' => $funding_instrument->getId(),
                'name' => $funding_instrument->getName()
            ];
        }

        return $result;
    }

    public function adGroupCategories()
    {
        return $this->api()->getAdGroupCategories();
    }

    public function store()
    {
        $api = $this->api();

        try {
            $promotable_users = $this->api()->getPromotableUsers();

            $campaign_data = $api->saveCampaign();
            $line_item_data = $api->saveLineItem($campaign_data);

            foreach (request('cards') as $card) {
                foreach ($card['media'] as $mediaPath) {
                    $media = $this->api()->uploadMedia($promotable_users, $mediaPath);
                    $media_library = $this->api()->createMediaLibrary($media->media_key);
                    $card_data = $api->createWebsiteCard($media->media_key, $card);

                    foreach ($card['tweetTexts'] as $tweetText) {
                        $tweet_data = $api->createTweet($card_data, $promotable_users, $card, $tweetText);
                        $promoted_tweet = $api->createPromotedTweet($line_item_data, $tweet_data);
                    }
                }
            }

            Helper::pullCampaign();
        } catch (Exception $e) {
            $this->rollback($campaign_data ?? null, $line_item_data ?? null, $card_data ?? null);
            if ($e instanceof TwitterAdsException && is_array($e->getErrors())) {
                return [
                    'errors' => [$e->getErrors()[0]->message]
                ];
            } else {
                return [
                    'errors' => [$e->getMessage()]
                ];
            }
        }

        return [];
    }

    private function rollback($campaign_data = null, $line_item_data = null, $card_data = null)
    {
        if ($campaign_data) {
            DeleteCampaign::dispatch(auth()->user(), $campaign_data->getId(), request('provider'), request('account'), request('advertiser'));
        }

        if ($line_item_data) {
            DeleteAdGroup::dispatch(auth()->user(), $line_item_data->getId(), request('provider'), request('account'), request('advertiser'));
        }

        if ($card_data) {
            DeleteCard::dispatch(auth()->user(), $card_data->getId(), request('provider'), request('account'), request('advertiser'));
        }
    }

    public function update(Campaign $campaign)
    {
        try {
            $api = new TwitterAPI(auth()->user()->providers()->where('provider_id', $campaign->provider_id)->where('open_id', $campaign->open_id)->first(), $campaign->advertiser_id);

            $campaign_data = $api->saveCampaign($campaign->campaign_id);
            $line_item_data = $api->saveLineItem($campaign_data, request('adGroupID'));

            if (!request('saveCard')) {
                $promotable_users = $api->getPromotableUsers();
                $promoted_tweets = $api->getPromotedTweets([$line_item_data->getId()]);

                if ($promoted_tweets && count($promoted_tweets) > 0) {
                    foreach ($promoted_tweets as $promoted_tweet) {
                        $api->deletePromotedTweet($promoted_tweet->getId());
                    }
                }

                foreach (request('cards') as $card) {
                    foreach ($card['media'] as $mediaPath) {
                        $media = $api->uploadMedia($promotable_users, $mediaPath);
                        $media_library = $api->createMediaLibrary($media->media_key);
                        $card_data = $api->createWebsiteCard($media->media_key, $card);

                        foreach ($card['tweetTexts'] as $tweetText) {
                            $tweet_data = $api->createTweet($card_data, $promotable_users, $card, $tweetText);
                            $promoted_tweet = $api->createPromotedTweet($line_item_data, $tweet_data);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return [
                'errors' => [$e->getMessage()]
            ];
        }

        return [];
    }

    public function storeAd(Campaign $campaign, $ad_group_id)
    {
        $api = new TwitterAPI(auth()->user()->providers()->where('provider_id', $campaign->provider_id)->where('open_id', $campaign->open_id)->first(), $campaign->advertiser_id);

        $promotable_users = $api->getPromotableUsers();
        $promoted_tweets = $api->getPromotedTweets([$ad_group_id]);

        if ($promoted_tweets && count($promoted_tweets) > 0) {
            foreach ($promoted_tweets as $promoted_tweet) {
                $api->deletePromotedTweet($promoted_tweet->getId());
            }
        }

        foreach (request('cards') as $card) {
            foreach ($card['media'] as $mediaPath) {
                $media = $api->uploadMedia($promotable_users, $mediaPath);
                $media_library = $api->createMediaLibrary($media->media_key);
                $card_data = $api->createWebsiteCard($media->media_key, $card);

                foreach ($card['tweetTexts'] as $tweetText) {
                    $tweet_data = $api->createTweet($card_data, $promotable_users, $card, $tweetText);
                    $promoted_tweet = $api->createPromotedTweet($line_item_data, $tweet_data);
                }
            }
        }

        return [];
    }

    public function delete(Campaign $campaign)
    {
        try {
            $api = new TwitterAPI(auth()->user()->providers()->where('provider_id', $campaign->provider_id)->where('open_id', $campaign->open_id)->first(), $campaign->advertiser_id);
            $api->deleteCampaign($campaign->campaign_id);
            $campaign->delete();

            return [];
        } catch (Exception $e) {
            return [
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function status(Campaign $campaign)
    {
        try {
            $api = new TwitterAPI(auth()->user()->providers()->where('provider_id', $campaign->provider_id)->where('open_id', $campaign->open_id)->first(), $campaign->advertiser_id);
            $campaign->status = $campaign->status == Campaign::STATUS_ACTIVE ? Campaign::STATUS_PAUSED : Campaign::STATUS_ACTIVE;

            $api->updateCampaignStatus($campaign);

            $ad_groups = $api->getAdGroups($campaign->campaign_id);

            if ($ad_groups && count($ad_groups) > 0) {
                foreach ($ad_groups as $ad_group) {
                    $api->updateAdGroupStatus($ad_group->getId(), $campaign->status);
                }
            }

            $campaign->save();

            return [];
        } catch (Exception $e) {
            return [
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function adGroupData(Campaign $campaign)
    {
        $api = new TwitterAPI(auth()->user()->providers()->where('provider_id', $campaign->provider_id)->where('open_id', $campaign->open_id)->first(), $campaign->advertiser_id);
        $ad_group_datas = $api->getAdGroups($campaign->campaign_id);

        $ad_groups = [];
        $ad_group_ids = [];
        $ads = [];

        if ($ad_group_datas && count($ad_group_datas) > 0) {
            foreach ($ad_group_datas as $ad_group) {
                $ad_groups[] = [
                    'id' => $ad_group->getId(),
                    'adGroupName' => $ad_group->getName(),
                    'advertiserId' => $campaign->advertiser_id,
                    'campaignId' => $campaign->campaign_id,
                    'startDateStr' => $ad_group->getStartTime() ? $ad_group->getStartTime()->format('Y-m-d') : '',
                    'endDateStr' => $ad_group->getEndTime() ? $ad_group->getEndTime()->format('Y-m-d') : '',
                    'status' => $ad_group->getEntityStatus()
                ];

                $ad_group_ids[] = $ad_group->getId();
            }

            $promoted_tweets = $api->getPromotedTweets($ad_group_ids);

            if ($promoted_tweets && count($promoted_tweets)) {
                foreach ($promoted_tweets as $promoted_tweet) {
                    $ads[] = [
                        'id' => $promoted_tweet->getId(),
                        'title' => $promoted_tweet->getId(),
                        'advertiserId' => $campaign->advertiser_id,
                        'campaignId' => $campaign->campaign_id,
                        'adGroupId' => $promoted_tweet->getLineItemId()
                    ];
                }
            }
        }

        return response()->json([
            'ad_groups' => $ad_groups,
            'ads' => $ads,
            'summary_data' => new \stdClass()
        ]);
    }

    public function adStatus(Campaign $campaign, $ad_group_id, $ad_id)
    {
        return [];
    }

    public function adGroupStatus(Campaign $campaign, $ad_group_id)
    {
        $api = new TwitterAPI(auth()->user()->providers()->where('provider_id', $campaign->provider_id)->where('open_id', $campaign->open_id)->first(), $campaign->advertiser_id);
        $status = request('status') == Campaign::STATUS_ACTIVE ? Campaign::STATUS_PAUSED : Campaign::STATUS_ACTIVE;

        try {
            $api->updateAdGroupStatus($ad_group_id, $status);

            return [];
        } catch (Exception $e) {
            return [
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function pullCampaign($user_provider)
    {
        $advertisers = (new TwitterAPI($user_provider))->getAdvertisers();

        $campaign_ids = [];

        foreach ($advertisers as $advertiser) {
            $campaigns = (new TwitterAPI($user_provider, $advertiser->getId()))->getCampaigns();

            if (is_array($campaigns)) {
                foreach ($campaigns as $item) {
                    $campaign = Campaign::firstOrNew([
                        'campaign_id' => $item->getId(),
                        'provider_id' => $user_provider->provider_id,
                        'user_id' => $user_provider->user_id,
                        'open_id' => $user_provider->open_id
                    ]);

                    $campaign->advertiser_id = $advertiser->getId();

                    $campaign->name = $item->getName();
                    $campaign->status = $item->getEntityStatus();
                    $campaign->budget = $item->getTotalBudgetAmountLocalMicro() ? ($item->getTotalBudgetAmountLocalMicro() / 1000000) : ($item->getDailyBudgetAmountLocalMicro() / 1000000);
                    $campaign->save();
                    $campaign_ids[] = $campaign->id;
                }
            }
        }

        Campaign::where([
            'user_id' => $user_provider->user_id,
            'provider_id' => $user_provider->provider_id,
            'open_id' => $user_provider->open_id
        ])->whereNotIn('id', $campaign_ids)->delete();
    }

    public function pullAdGroup($user_provider)
    {
        $ad_group_ids = [];

        Campaign::where('user_id', $user_provider->user_id)->where('provider_id', 3)->chunk(10, function ($campaigns) use ($user_provider, &$ad_group_ids) {
            foreach ($campaigns as $campaign) {
                $ad_groups = (new TwitterAPI($user_provider, $campaign->advertiser_id))->getAdGroups($campaign->campaign_id);
                foreach ($ad_groups as $ad_group) {
                    $db_ad_group = AdGroup::firstOrNew([
                        'ad_group_id' => $ad_group->getId(),
                        'user_id' => $user_provider->user_id,
                        'provider_id' => $user_provider->provider_id,
                        'campaign_id' => $campaign->campaign_id,
                        'advertiser_id' => $campaign->advertiser_id,
                        'open_id' => $user_provider->open_id
                    ]);

                    $db_ad_group->name = $ad_group->getName();
                    $db_ad_group->status = $ad_group->getEntityStatus();
                    $db_ad_group->save();
                    $ad_group_ids[] = $db_ad_group->id;
                }
            }
        });

        AdGroup::where([
            'user_id' => $user_provider->user_id,
            'provider_id' => $user_provider->provider_id,
            'open_id' => $user_provider->open_id
        ])->whereNotIn('id', $ad_group_ids)->delete();
    }

    public function pullAd($user_provider)
    {
        $ad_ids = [];
        AdGroup::where('user_id', $user_provider->user_id)->where('provider_id', 3)->chunk(10, function ($ad_groups) use ($user_provider, &$ad_ids) {
            foreach ($ad_groups as $key => $ad_group) {
                $ads = (new TwitterAPI($user_provider, $ad_group->advertiser_id))->getPromotedTweets([$ad_group->ad_group_id]);
                foreach ($ads as $key => $ad) {
                    $db_ad = Ad::firstOrNew([
                        'ad_id' => $ad->getId(),
                        'user_id' => $user_provider->user_id,
                        'provider_id' => $user_provider->provider_id,
                        'campaign_id' => $ad_group->campaign_id,
                        'advertiser_id' => $ad_group->advertiser_id,
                        'ad_group_id' => $ad_group->ad_group_id,
                        'open_id' => $user_provider->open_id
                    ]);

                    $db_ad->name = $ad->getTweetId();
                    $db_ad->status = $ad->getEntityStatus();
                    $db_ad->save();
                    $ad_ids[] = $db_ad->id;
                }
            }
        });

        Ad::where([
            'user_id' => $user_provider->user_id,
            'provider_id' => $user_provider->provider_id,
            'open_id' => $user_provider->open_id
        ])->whereNotIn('id', $ad_ids)->delete();
    }

    public function deleteCampaign(User $user, $campaign_id, $provider_slug, $account, $advertiser)
    {
        $provider = Provider::where('slug', $provider_slug)->first();
        (new TwitterAPI($user->providers()->where('provider_id', $provider->id)->where('open_id', $account)->first(), $advertiser))->deleteCampaign($campaign_id);
    }

    public function deleteAdGroup(User $user, $ad_group_id, $provider_slug, $account, $advertiser)
    {
        $provider = Provider::where('slug', $provider_slug)->first();
        (new TwitterAPI($user->providers()->where('provider_id', $provider->id)->where('open_id', $account)->first(), $advertiser))->deleteLineItem($ad_group_id);
    }

    public function deleteCard(User $user, $card_id, $provider_slug, $account, $advertiser)
    {
        $provider = Provider::where('slug', $provider_slug)->first();
        (new TwitterAPI($user->providers()->where('provider_id', $provider->id)->where('open_id', $account)->first(), $advertiser))->deleteCard($card_id);
    }

    public function pullRedTrack($campaign)
    {
        $tracker = UserTracker::where('provider_id', $campaign->provider_id)
            ->where('provider_open_id', $campaign->open_id)
            ->first();

        if ($tracker) {
            $client = new Client();
            $date = Carbon::now()->format('Y-m-d');
            $url = 'https://api.redtrack.io/report?api_key=' . $tracker->api_key . '&date_from=' . $date . '&date_to=' . $date . '&group=hour_of_day&sub3=[' . $campaign->campaign_id . ']&sub9=Twitter&tracks_view=true';
            $response = $client->get($url);

            $data = json_decode($response->getBody(), true);

            foreach ($data as $i => $value) {
                $value['date'] = $date;
                $value['user_id'] = $campaign->user_id;
                $value['campaign_id'] = $campaign->id;
                $value['provider_id'] = $campaign->provider_id;
                $value['open_id'] = $campaign->open_id;
                $redtrack_report = RedtrackReport::firstOrNew([
                    'date' => $date,
                    'sub3' => $campaign->campaign_id,
                    'hour_of_day' => $value['hour_of_day']
                ]);
                foreach (array_keys($value) as $array_key) {
                    $redtrack_report->{$array_key} = $value[$array_key];
                }
                $redtrack_report->save();
            }
        }
    }

    public function getSummaryDataQuery($data)
    {
        $summary_data_query = TwitterReport::select(
            DB::raw('ROUND(SUM(JSON_EXTRACT(data, "$[0].metrics.billed_charge_local_micro[0]") / 1000000), 2) as total_cost'),
            DB::raw('"N/A" as total_revenue'),
            DB::raw('"N/A" as total_net'),
            DB::raw('"N/A" as avg_roi')
        );
        $summary_data_query->leftJoin('campaigns', function ($join) use ($data) {
            $join->on('campaigns.id', '=', 'twitter_reports.campaign_id');
            if ($data['provider']) {
                $join->where('campaigns.provider_id', $data['provider']);
            }
            if ($data['account']) {
                $join->where('campaigns.open_id', $data['account']);
            }
        });
        $summary_data_query->whereBetween('end_time', [request('start'), request('end')]);

        return $summary_data_query;
    }

    public function getWidgetQuery($campaign, $data)
    {
        $widgets_query = TwitterReport::select([
            '*',
            DB::raw('JSON_EXTRACT(data, "$.summary.conversionMetrics[*].name") as widget_id'),
            DB::raw('NULL as calc_cpc'),
            DB::raw('NULL as tr_conv'),
            DB::raw('NULL as tr_rev'),
            DB::raw('NULL as tr_net'),
            DB::raw('NULL as tr_roi'),
            DB::raw('NULL as tr_epc'),
            DB::raw('NULL as epc'),
            DB::raw('NULL as tr_cpa'),
            DB::raw('NULL as clicks'),
            DB::raw('NULL as ts_clicks'),
            DB::raw('NULL as trk_clicks'),
            DB::raw('NULL as lp_clicks'),
            DB::raw('NULL as lp_ctr'),
            DB::raw('NULL as ctr'),
            DB::raw('NULL as tr_cvr'),
            DB::raw('NULL as ecpm'),
            DB::raw('NULL as lp_cr'),
            DB::raw('NULL as lp_cpc')
        ]);
        $widgets_query->where('campaign_id', $campaign->id);
        $widgets_query->whereBetween('end_time', [$data['start'], $data['end']]);
        $widgets_query->where(DB::raw('JSON_EXTRACT(data, "$.summary.conversionMetrics[*].name")'), 'LIKE', '%' . $data['search'] . '%');

        return $widgets_query;
    }

    public function getContentQuery($campaign, $data)
    {
        $contents_query = Ad::select([
            DB::raw('MAX(ads.id) as id'),
            DB::raw('MAX(ads.campaign_id) as campaign_id'),
            DB::raw('MAX(ads.ad_group_id) as ad_group_id'),
            DB::raw('MAX(ads.ad_id) as ad_id'),
            DB::raw('MAX(ads.name) as name'),
            DB::raw('MAX(ads.status) as status'),
            DB::raw('ROUND(SUM(total_revenue)/SUM(total_conversions), 2) as payout'),
            DB::raw('SUM(clicks) as clicks'),
            DB::raw('SUM(lp_views) as lp_views'),
            DB::raw('SUM(lp_clicks) as lp_clicks'),
            DB::raw('SUM(total_conversions) as total_conversions'),
            DB::raw('SUM(total_conversions) as total_actions'),
            DB::raw('ROUND((SUM(total_conversions)/SUM(clicks)) * 100, 2) as total_actions_cr'),
            DB::raw('ROUND((SUM(total_conversions)/SUM(clicks)) * 100, 2) as cr'),
            DB::raw('ROUND(SUM(total_revenue), 2) as total_revenue'),
            DB::raw('ROUND(SUM(cost), 2) as cost'),
            DB::raw('ROUND(SUM(profit), 2) as profit'),
            DB::raw('ROUND((SUM(profit)/SUM(cost)) * 100, 2) as roi'),
            DB::raw('ROUND(SUM(cost)/SUM(clicks), 2) as cpc'),
            DB::raw('ROUND(SUM(cost)/SUM(total_conversions), 2) as cpa'),
            DB::raw('ROUND(SUM(total_revenue)/SUM(clicks), 2) as epc'),
            DB::raw('ROUND((SUM(lp_clicks)/SUM(lp_views)) * 100, 2) as lp_ctr'),
            DB::raw('ROUND((SUM(total_conversions)/SUM(lp_views)) * 100, 2) as lp_views_cr'),
            DB::raw('ROUND((SUM(total_conversions)/SUM(lp_clicks)) * 100, 2) as lp_clicks_cr'),
            DB::raw('ROUND(SUM(cost)/SUM(lp_clicks), 2) as lp_cpc')
        ]);
        $contents_query->leftJoin('redtrack_content_stats', function ($join) use ($data) {
            $join->on('redtrack_content_stats.sub5', '=', 'ads.ad_id')->whereBetween('redtrack_content_stats.date', [$data['start'], $data['end']]);
        });
        $contents_query->where('ads.campaign_id', $campaign->campaign_id);
        $contents_query->where('name', 'LIKE', '%' . $data['search'] . '%');
        $contents_query->groupBy('ads.ad_id');

        return $contents_query;
    }

    public function getDomainQuery($campaign, $data)
    {
        //
    }
}
