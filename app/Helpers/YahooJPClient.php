<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Token;

class YahooJPClient
{
    private $user_info;

    public function __construct($user_info)
    {
        $this->user_info = $user_info;
    }

    public function call($method, $endpoint, $body = null)
    {
        $client = new Client();

        $url = env('YAHOOJP_DISPLAY_API_ENDPOINT') . '/' . $endpoint;

        $request_body = ['headers' => [
            'Authorization' => 'Bearer ' . $this->user_info->token,
            'Content-Type' => 'application/json'
        ]];

        if ($body) {
            $request_body['json'] = $body;
        }

        try {
            $response = $client->request($method, $url, $request_body);
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                Token::refreshYahooJP($this->user_info, function () use ($client, $method, $url, $request_body, &$response) {
                    $request_body['headers']['Authorization'] = 'Bearer ' . $this->user_info->token;
                    $response = $client->request($method, $url, $request_body);
                });
            } else {
                throw $e;
            }
        }

        return json_decode($response->getBody(), true);
    }

    public function upload($endpoint, $body, $file, $file_name)
    {
        $client = new Client();

        $url = env('YAHOOJP_DISPLAY_API_ENDPOINT') . '/' . $endpoint;

        $request_body = [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->user_info->token,
                'Content-Type' => 'multipart/form-data'
            ],
            'multipart' => [[
                'name' => 'accountId',
                'contents' => $body['accountId'],
            ], [
                'name' => 'videoName',
                'contents' => $body['videoName'],
            ], [
                'name' => 'videoTitle',
                'contents' => $body['videoTitle'],
            ], [
                'name' => 'userStatus',
                'contents' => $body['userStatus'],
            ], [
                'name' => 'file',
                'contents' => $file,
                'filename' => $file_name
            ]]
        ];

        $response = $client->request('POST', $url, $request_body);

        return json_decode($response->getBody(), true);
    }
}
