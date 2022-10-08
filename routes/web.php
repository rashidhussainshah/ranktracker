<?php

use App\Models\SearchIteration;
use App\Services\DataForSEO;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (DataForSEO $dataForSEO) {
    Artisan::call('optimize');
    $us = \App\Models\UserSearch::first();
    //

    $requestData = [];
    $dfs = new DataForSEO();

//    for ($i = 1; $i <= $us->search_repetitions ;$i++) {
//        $data['language_code'] = 'en';
//        $data['location_name'] = $us->country;
//        $data['device'] = $us->device;
//        $data['keyword'] = $us->keyword;
//        $data['priority'] = config('dataforseo.priority');
//        $data['postback_url'] = url('api/postbackscript');
//        $data['postback_data'] = 'regular';
//        array_push($requestData, $data);
//    }
//
//
//    $api_response = $dfs->createTask($requestData);
//    if($api_response->status_code == 20000 && $api_response->tasks_error == 0){
//        foreach ($api_response->tasks as $task){
//            if($task->status_code == 20100 && $task->status_message == "Task Created."){
//
//                SearchIteration::create([
//                    'user_search_id' => $us->id,
//                    'task_id' => $task->id,
//                    'created_at' => now(),
//                    'updated_at' => now()
//                ]);
//
//            }else{
//                Log::error("Error in API response posting task.");
//            }
//        }
//    }else{
//        Log::error("Error in API response posting task.");
//    }
//dd('done');
    //



    $api_response = $dataForSEO->searchKeywords('make a cake', 'United Kingdom');
    $si = new SearchIteration();
    $si->user_search_id = $us->id;
    $si->search_results = json_encode($api_response);
    $si->task_id = '03041632-2521-0066-0000-922524ab1ce6';
    $si->save();
    $search_results = [];
    if($api_response->status_code == 20000 && $api_response->tasks_error == 0){

//        foreach ($api_response->tasks as $task){
//            if($task->status_code == 20000 && $task->status_message == "Ok."){
//                foreach ($task->result as $result){
//                    if($result->type == 'organic' && $result->items_count > 0){
//                        foreach ($result->items as $item){
////                            $search_results[] = (array) $item;
//                        }
//                    }
//                }
//            }
//        }
    }
//    $number_of_results = !$report->search_results?config('data-for-seo.save_number_of_results'):$report->search_results;
    $number_of_results = 5;
    $search_results_limited = array_slice($search_results,0,$number_of_results);

//    $us->searchResults()->createMany($search_results_limited);

    // create task

    return view('welcome');
});


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
