<?php

namespace App\Utils\AdVendors;

use Exception;

use App\Models\Campaign;
use App\Models\Provider;
use App\Endpoints\TwitterAPI;

class Twitter extends Root
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

    public function signUp()
    {
        $account = $this->api()->createAccount();

        return [
            'id' => $account->getId(),
            'name' => $account->getName()
        ];
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

    public function media()
    {
        //$media = $this->api()->uploadMedia();

        return $this->api()->createMediaLibrary('3_1330913410511298560');
    }

    public function store()
    {
        $data = [];
        $provider = Provider::where('slug', request('provider'))->first();
        $user_info = auth()->user()->providers()->where('provider_id', $provider->id)->where('open_id', request('account'))->first();

        $api = new TwitterAPI($user_info, request('advertiser') ?? null);

        try {
            $campaign_data = $api->createCampaign();

            $campaign = Campaign::firstOrNew([
                'campaign_id' => $campaign_data->getId(),
                'provider_id' => $provider->id,
                'open_id' => $user_info->open_id,
                'user_id' => auth()->id()
            ]);

            try {
                $line_item_data = $api->createLineItem($campaign_data);
            } catch (Exception $e) {
                throw $e;
            }

            try {
                $card_data = $api->createCard();
            } catch (Exception $e) {
                throw $e;
            }



        } catch (Exception $e) {
            var_dump($e);
            $data = [
                'errors' => [$e->getMessage()]
            ];
        }

        return $data;
    }
}
