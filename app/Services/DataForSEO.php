<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DataForSEO extends BaseDFSO
{
    /**
     * @return void
     */
    public function authentication()
    {

    }

    /**
     * @param string $keyword
     * @param string $country
     * @return mixed|void
     */
    public function searchKeywords(string $keyword, string $country)
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
