<?php

namespace App\Endpoints;

use Carbon\Carbon;
use App\Helpers\TaboolaClient;

class TaboolaAPI
{
    private $client;

    private $account_id;

    public function __construct($user_info)
    {
        $this->account_id = $user_info->open_id;

        $this->client = new TaboolaClient($user_info);
    }

    public function getAdvertisers()
    {
        return $this->client->call('GET', $this->account_id . '/advertisers');
    }

    public function getCampaigns($advertiser_id)
    {
        return $this->client->call('GET', $advertiser_id . '/campaigns/?fetch_level=R');
    }

    public function getCampaign($advertiser_id, $campaign_id)
    {
        return $this->client->call('GET', $advertiser_id . '/campaigns/' . $campaign_id);
    }

    public function getCountries()
    {
        return $this->client->call('GET', 'resources/countries');
    }

    public function createCampaign($advertiser_id, $data)
    {
        return $this->client->call('POST', $advertiser_id . '/campaigns', $data);
    }

    public function updateCampaign($advertiser_id, $campaign_id, $data)
    {
        return $this->client->call('PUT', $advertiser_id . '/campaigns/' . $campaign_id, $data);
    }

    public function createCampaignItem($advertiser_id, $campaign_id, $url)
    {
        return $this->client->call('POST', $advertiser_id . '/campaigns/' . $campaign_id . '/items', [
            'url' => $url
        ]);
    }

    public function updateCampaignItem($advertiser_id, $campaign_id, $campaign_item)
    {
        return $this->client->call('PUT', $advertiser_id . '/campaigns/' . $campaign_id . '/items/' . $campaign_item['id'], [
            'url' => $campaign_item['url'],
            'title' => $campaign_item['title'],
            'description' => $campaign_item['description'],
            'thumbnail_url' => $campaign_item['thumbnail_url']
        ]);
    }

    public function getCampaignItems($advertiser_id, $campaign_id)
    {
        return $this->client->call('GET', $advertiser_id . '/campaigns/' . $campaign_id . '/items');
    }

    public function deleteCampaign($advertiser_id, $campaign_id)
    {
        return $this->client->call('DELETE', $advertiser_id . '/campaigns/' . $campaign_id);
    }
}