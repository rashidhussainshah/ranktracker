<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class DataForSEO
{
    public function authentication()
    {

    }

    public function searchKeywords($keyword, $country)
    {
        try {
            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic bGFyYXZlbC5leHBlcnQ3ODZAZ21haWwuY29tOjg3MDA0ODcyZTk5NGEwN2Q='
            ];

            $body = '[
                      {
                        "language_name": "English (United Kingdom)",
                        "location_name": "' . $country . '",
                        "keyword": "' . $keyword . '"
                      }
                  ]';
            $request = new Request('POST', 'https://api.dataforseo.com/v3/serp/google/organic/live/regular', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            Log::info('api response');
            Log::info(json_encode(json_decode($res->getBody())));
            return json_decode($res->getBody());
        }catch (\Exception $exception) {
            Log::info('searchKeywords Exception');
            Log::info($exception->getMessage());
        }
    }

    public function createTask($data)
    {
        try {
            $client = new Client();
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic bGFyYXZlbC5leHBlcnQ3ODZAZ21haWwuY29tOjg3MDA0ODcyZTk5NGEwN2Q='
            ];
            $body = '[
            {
             "language_code": "en",
             "location_code": 2840,
             "keyword": "albert einstein",
             "device": "desktop",
             "tag": "some_string_123",
             "postback_url": "https://your-server.com/postbackscript.php",
             "postback_data": "advanced"
  }
]';
            $request = new Request('POST', 'https://api.dataforseo.com/v3/serp/google/organic/task_post', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            return json_decode($res->getBody());
        }catch (\Exception $exception) {
            Log::info('createTask Exception');
            dd($exception->getMessage());
            Log::info($exception->getMessage());
        }
    }
}
