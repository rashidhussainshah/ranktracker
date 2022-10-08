<?php

namespace App\Services;

use App\Models\SearchIteration;
use Illuminate\Support\Facades\Log;

class SearchIterationService
{
    public function getVolatilityData($request, $report, $country){
        $requestData = [];
        $dfs = new DataForSEO();

        for ($i = 1; $i <= $request->iterations ;$i++) {
            $data['language_code'] = 'en';
            $data['location_name'] = $country->name;
            $data['device'] = $request->device;
            $data['keyword'] = $request->keyword;
            $data['priority'] = config('data-for-seo.priority');
            $data['postback_url'] = url('api/postbackscript');
            $data['postback_data'] = 'regular';
            array_push($requestData, $data);
        }


        $api_response = $dfs->createTask($requestData);

        if($api_response['status_code'] == 20000 && $api_response['tasks_error'] == 0){
            foreach ($api_response['tasks'] as $task){
                if($task['status_code'] == 20100 && $task['status_message'] == "Task Created."){

                    Log::info('Posted Task###################', $task);
                    SearchIteration::create([
                        'report_id' => $report->id,
                        'task_id' => $task['id']
                    ]);

                }else{
                    Log::error("Error in API response posting task.");
                }
            }
        }else{
            Log::error("Error in API response posting task.");
        }

    }

}
