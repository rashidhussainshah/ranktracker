<?php

namespace App\Services;

use App\Models\UserSearch;

class KeywordSearchResult
{
    public function getSaveSearchResults(UserSearch $userSearch){
        $dataForSEO = new DataForSEO();
        $api_response = $dataForSEO->searchKeywords($userSearch->keyword, $userSearch->country);
        $search_results = [];
        if($api_response->status_code == 20000 && $api_response->tasks_error == 0){
            foreach ($api_response->tasks as $task){
                if($task->status_code == 20000 && $task->status_message == "Ok."){
                    foreach ($task->result as $result){
                        if($result->type == 'organic' && $result->items_count > 0){
                            foreach ($result->items as $item){
                                $search_results[] = (array) $item;
                            }
                        }
                    }
                }
            }
        }
//    $number_of_results = !$report->search_results?config('data-for-seo.save_number_of_results'):$report->search_results;
        $number_of_results = 5;
        $search_results_limited = array_slice($search_results,0,$number_of_results);

        $userSearch->searchResults()->createMany($search_results_limited);

    }

}
