<?php

namespace App\Utils\AdVendors;

use Exception;

use App\Endpoints\OutbrainAPI;

use App\Models\Provider;
use App\Models\OutbrainCampaign;

use GuzzleHttp\Exception\GuzzleException;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Jobs\PullCampaign;

class Outbrain extends Root
{
    private function api()
    {
        $provider = Provider::where('slug', request('provider'))->first();
        return new OutbrainAPI(auth()->user()->providers()->where('provider_id', $provider->id)->where('open_id', request('account'))->first());
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

            $campaign = OutbrainCampaign::firstOrNew([
                'campaign_id' => $data['id'],
                'provider_id' => $user_provider->provider_id,
                'open_id' => $user_provider->open_id,
                'user_id' => $user_provider->user_id
            ]);

            unset($data['id']);
            foreach (array_keys($data) as $index => $array_key) {
                $campaign->{$array_key} = $data[$array_key];
            }

            $campaign->save();
        });
    }
}
