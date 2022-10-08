<?php

namespace App\Jobs;

use App\Models\UserSearch;
use App\Services\DataForSEO;
use App\Services\KeywordSearchResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserSearchRepetation implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var UserSearch
     */
    private $userSearch;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserSearch $userSearch)
    {
        $this->userSearch = $userSearch;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(KeywordSearchResult $keywordSearchResult)
    {
        $keywordSearchResult->getSaveSearchResults($this->report);
//        Log::info(json_encode($this->data));
//        $dfseo = new DataForSEO();
//        if (isset($this->data['search_repetitions'])) {
//            for ($i = 1; $i <= $this->data['search_repetitions']; $i++) {
//                $apiResponse = $dfseo->searchKeywords($this->data['location_name'], $this->data['keyword']);
//            }
//        }
    }
}
