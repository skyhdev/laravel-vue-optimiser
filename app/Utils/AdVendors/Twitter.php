<?php

namespace App\Utils\AdVendors;

use Exception;

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

    public function createFundingInstrument()
    {
        $fundingInstrument = $this->api()->createFundingInstrument([
            'currency' => request('fundingInstrumentCurrency'),
            'start_time' => request('fundingInstrumentStartTime'),
            'type' => request('fundingInstrumentType'),
        ]);
    }
}
