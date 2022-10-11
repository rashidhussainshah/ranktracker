<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class DataForSEO extends BaseDFSO
{
    /**
     * Search Keyword API Request
     * @param $keyword
     * @param $country
     * @return mixed|void
     */
    public function searchKeywords($keyword, $country)
    {
        try {
            $this->setBody(
                '[
                      {
                        "language_name": "English (United Kingdom)",
                        "location_name": "' . $country . '",
                        "keyword": "' . $keyword . '"
                      }
                  ]'
            );
            return $this->postReq('serp/google/organic/live/regular');
        } catch (\Exception $exception) {
            Log::info('searchKeywords Exception');
            Log::info($exception->getMessage());
        }
    }
}
