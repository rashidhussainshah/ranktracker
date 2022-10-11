<?php

namespace App\Jobs;

use App\Models\SearchIteration;
use App\Models\UserSearch;
use App\Services\DataForSEO;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserSearchRepetition implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userSearch;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userSearch)
    {
        $this->userSearch = $userSearch;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DataForSEO $dfseo)
    {
        try {
            if (isset($this->userSearch->search_repetitions)) {
                for ($i = 1; $i <= $this->userSearch->search_repetitions; $i++) {
                    $apiResponse = $dfseo->searchKeywords($this->userSearch->keyword, $this->userSearch->country);
                    if ($apiResponse->status_code == 20000 && $apiResponse->tasks_error == 0) {
                        $si = new SearchIteration();
                        $si->user_search_id = $this->userSearch->id;
                        $si->search_results = json_encode($apiResponse);
                        $si->iteration = $i;
                        $si->save();
                    }
                }
                $this->userSearch->status = UserSearch::STATUS_COMPLETE;
                $this->userSearch->save();
            }
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
