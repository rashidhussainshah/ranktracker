<?php

namespace App\Http\Controllers;

use App\Jobs\UserSearchRepetition;
use App\Models\SearchIteration;
use App\Models\UserSearch;
use App\Models\UserSearchKeyword;
use App\Services\DataForSEO;
use Carbon\Carbon;
use Facade\FlareClient\Report;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class UserSearchController extends VoyagerBaseController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));
        // save DataForSEO results
        if ((config('dataforseo.enable_queue') == 'yes')) {
            dispatch(new UserSearchRepetition($data));
        } else {
            $this->saveUserSearchApiRes($data);
        }

        if (!$request->has('_tagging')) {
            if (auth()->user()->can('browse', $data)) {
                $redirect = redirect()->route("voyager.{$dataType->slug}.index");
            } else {
                $redirect = redirect()->back();
            }

            return $redirect->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
        } else {
            return response()->json(['success' => true, 'data' => $data]);
        }
    }


    //***************************************
    //                _____
    //               |  __ \
    //               | |__) |
    //               |  _  /
    //               | | \ \
    //               |_|  \_\
    //
    //  Read an item of our Data Type B(R)EAD
    //
    //****************************************

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws AuthorizationException
     */
    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $isSoftDeleted = false;

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);
            $query = $model->query();

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $query = $query->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $query->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$query, 'findOrFail'], $id);
            if ($dataTypeContent->deleted_at) {
                $isSoftDeleted = true;
            }
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType, true);

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'read', $isModelTranslatable);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        // DataForSEO Graph and Table Data Preparation
        $us = UserSearch::find($dataTypeContent->id);
        $iterations = $us->searchIterations()->orderBy('updated_at', 'desc')->get();
        $data = [];
        $count = [];
        $total = $iterations->count();
        $completed = $iterations->where('search_results', '!=', null)->count();

        foreach ($iterations as $index => $iteration) {
            if (!is_null($iteration->search_results)) {
                $searchResults = json_decode($iteration->search_results, true);
                $items = $searchResults['tasks'][0]['result'][0]['items'];
                $data = array_merge($data, $items);
                $count[] =  $iteration->iteration;
            }
        }
        $graphData = [];
        $tableData = [];
        $groupedData = collect($data)->groupBy('url');
        $boxplot_data = [];
        $boxplot_data_label = [];
        $boxplot_cj_data = [];
        foreach ($groupedData as $site => $group) {
            $color = sprintf('#%06X', mt_rand(1, 0xFFFFFF));
            $tableData[$site] = [
                'title' => $group[0]['title'],
                'description' => $group[0]['description'],
                'url' => $group[0]['url'],
                'domain' => $group[0]['domain'],
                'ranks' => []
            ];
            $data = [
                'label' => "$site",
                'data' => [],
                'lineTension' => 0,
                'fill' => false,
                'borderWidth' => 1.5,
                'borderColor' => $color,
                'backgroundColor' => $color
            ];

            foreach ($iterations as $iteration) {
                if (!is_null($iteration->search_results)) {
                    $searchResults = json_decode($iteration->search_results, true);
                    $items = collect($searchResults['tasks'][0]['result'][0]['items'])->where('url', $site);
                    $rank = [];
                    if (count($items) == 0) {
                        $rank[] = 'X';
                    }
                    foreach ($items as $item) {
                        if ($item['type'] != 'organic')
                            continue;
                        $data['data'][] = [
                            'x' =>  $iteration->iteration,
                            'y' => $item['rank_group']
                        ];
                        $rank[] = $item['rank_group'];
                    }
                }
            }
            $graphData[] = $data;
        }

        // Box plot prepare data
        foreach ($tableData as $url => $url_data) {
            $url_data['ranks']=   array_map(static function (string $value): string {
                return (int)$value == 0 ? 'X' : $value;
            }, $url_data['ranks']);
            $boxplot_arr = array_filter(array_map('intval', $url_data['ranks']));
            if(count($boxplot_arr)>0){
                $url_data['ranks']=   array_map(static function (string $value) use ($boxplot_arr): string {
                    return $value == 'X' ? array_shift($boxplot_arr) : $value;
                }, $url_data['ranks']);
                $boxplot_data[] = $url_data['ranks'];
                $boxplot_data_label[] = $url_data['url']; //$url_data['ranks']

            }

        }

        $boxplot_cj_data_arr = array_slice($boxplot_cj_data, 0, 5, true);

        usort($boxplot_data, function($a, $b) {
            return $a <=> $b;
        });

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable', 'isSoftDeleted'))->with(['id' => $us->id, 'graphData' => ['labels' => $count, 'datasets' => $graphData], 'tableData' => $tableData, 'total' => $total, 'completed' => $completed, 'boxpot_data' => $boxplot_data, 'boxpot_data_labels' => $boxplot_data_label, 'boxplot_cj_data'=>$boxplot_cj_data_arr]);;
    }

    /**
     * Save User Search DataForSEO API Response
     * @param $userSearch
     * @return void
     */
    private function saveUserSearchApiRes($userSearch)
    {
        try {
            $dfseo = new DataForSEO();
            if (isset($userSearch->search_repetitions)) {
                for ($i = 1; $i <= $userSearch->search_repetitions; $i++) {
                    $apiResponse = $dfseo->searchKeywords($userSearch->keyword,$userSearch->country);
                    if($apiResponse->status_code == 20000 && $apiResponse->tasks_error == 0){
                        $si = new SearchIteration();
                        $si->user_search_id = $userSearch->id;
                        $si->search_results = json_encode($apiResponse);
                        $si->iteration = $i;
                        $si->save();
                    }
                }
                $userSearch->status = UserSearch::STATUS_COMPLETE;
                $userSearch->save();
            }
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }

}
