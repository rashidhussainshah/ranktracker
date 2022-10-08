<?php

return [
    /*
     * Data for SEO configuration.
     */
    'sandbox_host' => env('DATA_FOR_SEO_SANDBOX_HOST'),
    'live_host' => env('DATA_FOR_SEO_HOST'),
    'email' => env('DATA_FOR_SEO_EMAIL'),
    'password' => env('DATA_FOR_SEO_PASSWORD'),
    'save_number_of_results' => env('DATA_FOR_SEO_SAVE_RESULTS'),
    'priority' => env('PRIORITY', 1),
    'version' => env('VERSION', 'v3'),
];
