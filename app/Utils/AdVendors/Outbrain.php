<?php

namespace App\Utils\AdVendors;

use Carbon\Carbon;
use GuzzleHttp\Client;

use App\Models\Provider;
use App\Models\Campaign;
use App\Models\UserTracker;
use App\Models\RedtrackReport;

use App\Jobs\PullCampaign;

use App\Endpoints\OutbrainAPI;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Exception\GuzzleException;

class Outbrain extends Root
{
    private function api()
    {
        $provider = Provider::where('slug', request('provider'))->first();
        return new OutbrainAPI(auth()->user()->providers()->where('provider_id', $provider->id)->where('open_id', request('account'))->first());
    }

    public function languages()
    {
        return config('constants.languages');
    }

    public function countries()
    {
        return $this->api()->getCountries();
    }

    /**
     * @return mixed
     * @throws GuzzleException
     */
    public function advertisers()
    {
        return $this->api()->getMarketers();
    }

    public function store()
    {
        $data = [];
        $api = $this->api();

        try {
            $budget_data = $api->createBudget();
            Log::info('OUTBRAIN: Created budget: ' . $budget_data['id']);

            try {
                $campaign_data = $api->createAdCampaign($budget_data);
                Log::info('OUTBRAIN: Created campaign: ' . $campaign_data['id']);
            } catch (Exception $e) {
                $api->deleteBudget($budget_data);
                throw $e;
            }

            try {
                $ad_data = $api->createAd($campaign_data);
                Log::info('OUTBRAIN: Created ad: ' . $ad_data['id']);
            } catch (Exception $e) {
                $api->deleteCampaign($campaign_data);
                $api->deleteBudget($budget_data);
                throw $e;
            }

            PullCampaign::dispatch(auth()->user());
        } catch (Exception $e) {
            $data = [
                'errors' => [$e->getMessage()]
            ];
        }

        return $data;
    }

    public function pullCampaign($user_provider)
    {
        $api = new OutbrainAPI($user_provider);

        $marketers_ids = collect($api->getMarketers()['marketers'])->pluck('id');
        $campaigns = collect([]);

        $marketers_ids->each(function ($id) use (&$campaigns, $api) {
            $campaigns_by_marketer = $api->getCampaignsByMarketerId($id);
            if (array_key_exists('campaigns', $campaigns_by_marketer)) {
                $campaigns_by_marketer = $campaigns_by_marketer['campaigns'];
                foreach ($campaigns_by_marketer as $campaign) {
                    $campaigns->push($campaign);
                }
            }
        });

        $campaigns->each(function ($item) use ($user_provider) {
            $data = collect($item)->keyBy(function ($value, $key) {
                return Str::of($key)->snake();
            })->toArray();

            $campaign = Campaign::firstOrNew([
                'campaign_id' => $data['id'],
                'provider_id' => $user_provider->provider_id,
                'open_id' => $user_provider->open_id,
                'user_id' => $user_provider->user_id
            ]);

            // unset($data['id']);
            // foreach (array_keys($data) as $index => $array_key) {
            //     $campaign->{$array_key} = $data[$array_key];
            // }

            $campaign->save();
        });
    }

    public function pullRedTrack($campaign)
    {
        $tracker = UserTracker::where('provider_id', $this->campaign->provider_id)
            ->where('provider_open_id', $this->campaign->open_id)
            ->first();

        if ($tracker) {
            $client = new Client();
            $date = Carbon::now()->format('Y-m-d');
            $url = 'https://api.redtrack.io/report?api_key=' . $tracker->api_key . '&date_from=' . $date . '&date_to=' . $date . '&group=hour_of_day&sub5=' . $this->campaign->campaign_id . '&tracks_view=true';
            $response = $client->get($url);

            $data = json_decode($response->getBody(), true);

            foreach ($data as $i => $value) {
                $value['date'] = $date;
                $value['user_id'] = $this->campaign->user_id;
                $value['campaign_id'] = $this->campaign->id;
                $value['provider_id'] = $this->campaign->provider_id;
                $value['open_id'] = $this->campaign->open_id;
                $redtrack_report = RedtrackReport::firstOrNew([
                    'date' => $date,
                    'sub5' => $this->campaign->campaign_id,
                    'hour_of_day' => $value['hour_of_day']
                ]);
                foreach (array_keys($value) as $array_key) {
                    $redtrack_report->{$array_key} = $value[$array_key];
                }
                $redtrack_report->save();
            }

            // $url = 'https://api.redtrack.io/report?api_key=' . $tracker->api_key . '&date_from=' . $date . '&date_to=' . $date . '&group=sub5=' . $this->campaign->campaign_id . '&tracks_view=true';
            // $response = $client->get($url);

            // $data = json_decode($response->getBody(), true);

            // foreach ($data as $i => $value) {
            //     $value['date'] = $date;
            //     $value['user_id'] = $this->campaign->user_id;
            //     $value['campaign_id'] = $this->campaign->id;
            //     $value['provider_id'] = $this->campaign->provider_id;
            //     $value['open_id'] = $this->campaign->open_id;
            //     $redtrack_report = RedtrackDomainStat::firstOrNew([
            //         'date' => $date,
            //     ]);
            //     foreach (array_keys($value) as $array_key) {
            //         $redtrack_report->{$array_key} = $value[$array_key];
            //     }
            //     $redtrack_report->save();
            // }
        }
    }
}
