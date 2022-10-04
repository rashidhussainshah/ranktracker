<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class DataForSEO
{
    public function authentication()
    {

    }

    public function searchKeywords($request)
    {

        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic bGFyYXZlbC5leHBlcnQ3ODZAZ21haWwuY29tOjg3MDA0ODcyZTk5NGEwN2Q='
        ];

        $body = '[
                      {
                        "language_name": "English (United Kingdom)",
                        "location_name": "' . $request->country . '",
                        "keyword": "' . $request->keyword . '"
                      }
                  ]';
        $request = new Request('POST', 'https://api.dataforseo.com/v3/serp/google/organic/live/advanced', $headers, $body);
        $res = $client->sendAsync($request)->wait();
        dd(json_decode($res->getBody()));

    }
}
