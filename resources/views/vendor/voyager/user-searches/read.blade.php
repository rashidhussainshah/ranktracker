@extends('voyager::master')

@section('page_title', __('voyager::generic.view').' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i> {{ __('voyager::generic.viewing') }} {{ ucfirst($dataType->getTranslatedAttribute('display_name_singular')) }} &nbsp;

        @can('edit', $dataTypeContent)
            <a href="{{ route('voyager.'.$dataType->slug.'.edit', $dataTypeContent->getKey()) }}" class="btn btn-info">
                <i class="glyphicon glyphicon-pencil"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.edit') }}</span>
            </a>
        @endcan
        @can('delete', $dataTypeContent)
            @if($isSoftDeleted)
                <a href="{{ route('voyager.'.$dataType->slug.'.restore', $dataTypeContent->getKey()) }}" title="{{ __('voyager::generic.restore') }}" class="btn btn-default restore" data-id="{{ $dataTypeContent->getKey() }}" id="restore-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.restore') }}</span>
                </a>
            @else
                <a href="javascript:;" title="{{ __('voyager::generic.delete') }}" class="btn btn-danger delete" data-id="{{ $dataTypeContent->getKey() }}" id="delete-{{ $dataTypeContent->getKey() }}">
                    <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.delete') }}</span>
                </a>
            @endif
        @endcan
        @can('browse', $dataTypeContent)
        <a href="{{ route('voyager.'.$dataType->slug.'.index') }}" class="btn btn-warning">
            <i class="glyphicon glyphicon-list"></i> <span class="hidden-xs hidden-sm">{{ __('voyager::generic.return_to_list') }}</span>
        </a>
        @endcan
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <!-- Line Chart -->
    <div class="row">
        <div class="col-lg-12 col-xl-6">
            <!--Table bordered-->
            <div id="panel-2" class="panel">
                <div class="panel-hdr">
                    <h2>
                        Keyword Rank Chart
                    </h2>

                </div>
                <div class="panel-container show">
                    <div class="panel-content">
                        <canvas id="rankChart" width="600" height="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <!--Table bordered-->
            <div id="panel-4" class="panel">
                <div class="panel-hdr">
                    <h2>
                        Keyword Rank Table
                    </h2>

                </div>
                <div class="panel-container show">
                    <div class="panel-content">

                        <table class="table table-bordered m-0">
                            <thead>
                            <tr>
                                <th>Keyword</th>
                                <th>Description</th>
                                <th>Website</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($tableData) == 0)
                                <tr>
                                    <td colspan="5" style="text-align: center">No results found</td>
                                </tr>
                            @endif
                            @php
                                $i = 0;
                            @endphp
                            @foreach($tableData as $key => $search)

                                <tr>
                                    <td style=" width: 20%;">{{$search['title']}}</td>
                                    <td style=" width: 20%; word-wrap: break-word;min-width: 160px;max-width: 160px;">{{$search['description']}}</td>
                                    <td style=" width: 20%; word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                        <a target="_blank" href="{{$search['url']}}">{{$search['url']}}</a><br>

                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- solid sales graph -->
{{--    <div class="card bg-gradient-info">--}}
{{--        <div class="card-header border-0">--}}
{{--            <h3 class="card-title">--}}
{{--                <i class="fas fa-th mr-1"></i>--}}
{{--                Sales Graph--}}
{{--            </h3>--}}

{{--            <div class="card-tools">--}}
{{--                <button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">--}}
{{--                    <i class="fas fa-minus"></i>--}}
{{--                </button>--}}
{{--                <button type="button" class="btn bg-info btn-sm" data-card-widget="remove">--}}
{{--                    <i class="fas fa-times"></i>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="card-body">--}}
{{--            <canvas class="chart" id="line-chart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>--}}
{{--        </div>--}}
{{--        <!-- /.card-body -->--}}
{{--        <div class="card-footer bg-transparent">--}}
{{--            <div class="row">--}}
{{--            </div>--}}
{{--            <!-- /.row -->--}}
{{--        </div>--}}
{{--        <!-- /.card-footer -->--}}
{{--    </div>--}}
    <br>
    <!-- /.card -->
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <!-- form start -->
                    @foreach($dataType->readRows as $row)
                        @php
                        if ($dataTypeContent->{$row->field.'_read'}) {
                            $dataTypeContent->{$row->field} = $dataTypeContent->{$row->field.'_read'};
                        }
                        @endphp
                        <div class="panel-heading" style="border-bottom:0;">
                            <h3 class="panel-title">{{ $row->getTranslatedAttribute('display_name') }}</h3>
                        </div>

                        <div class="panel-body" style="padding-top:0;">
                            @if (isset($row->details->view))
                                @include($row->details->view, ['row' => $row, 'dataType' => $dataType, 'dataTypeContent' => $dataTypeContent, 'content' => $dataTypeContent->{$row->field}, 'action' => 'read', 'view' => 'read', 'options' => $row->details])
                            @elseif($row->type == "image")
                                <img class="img-responsive"
                                     src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                            @elseif($row->type == 'multiple_images')
                                @if(json_decode($dataTypeContent->{$row->field}))
                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                        <img class="img-responsive"
                                             src="{{ filter_var($file, FILTER_VALIDATE_URL) ? $file : Voyager::image($file) }}">
                                    @endforeach
                                @else
                                    <img class="img-responsive"
                                         src="{{ filter_var($dataTypeContent->{$row->field}, FILTER_VALIDATE_URL) ? $dataTypeContent->{$row->field} : Voyager::image($dataTypeContent->{$row->field}) }}">
                                @endif
                            @elseif($row->type == 'relationship')
                                 @include('voyager::formfields.relationship', ['view' => 'read', 'options' => $row->details])
                            @elseif($row->type == 'select_dropdown' && property_exists($row->details, 'options') &&
                                    !empty($row->details->options->{$dataTypeContent->{$row->field}})
                            )
                                <?php echo $row->details->options->{$dataTypeContent->{$row->field}};?>
                            @elseif($row->type == 'select_multiple')
                                @if(property_exists($row->details, 'relationship'))

                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                        {{ $item->{$row->field}  }}
                                    @endforeach

                                @elseif(property_exists($row->details, 'options'))
                                    @if (!empty(json_decode($dataTypeContent->{$row->field})))
                                        @foreach(json_decode($dataTypeContent->{$row->field}) as $item)
                                            @if (@$row->details->options->{$item})
                                                {{ $row->details->options->{$item} . (!$loop->last ? ', ' : '') }}
                                            @endif
                                        @endforeach
                                    @else
                                        {{ __('voyager::generic.none') }}
                                    @endif
                                @endif
                            @elseif($row->type == 'date' || $row->type == 'timestamp')
                                @if ( property_exists($row->details, 'format') && !is_null($dataTypeContent->{$row->field}) )
                                    {{ \Carbon\Carbon::parse($dataTypeContent->{$row->field})->formatLocalized($row->details->format) }}
                                @else
                                    {{ $dataTypeContent->{$row->field} }}
                                @endif
                            @elseif($row->type == 'checkbox')
                                @if(property_exists($row->details, 'on') && property_exists($row->details, 'off'))
                                    @if($dataTypeContent->{$row->field})
                                    <span class="label label-info">{{ $row->details->on }}</span>
                                    @else
                                    <span class="label label-primary">{{ $row->details->off }}</span>
                                    @endif
                                @else
                                {{ $dataTypeContent->{$row->field} }}
                                @endif
                            @elseif($row->type == 'color')
                                <span class="badge badge-lg" style="background-color: {{ $dataTypeContent->{$row->field} }}">{{ $dataTypeContent->{$row->field} }}</span>
                            @elseif($row->type == 'coordinates')
                                @include('voyager::partials.coordinates')
                            @elseif($row->type == 'rich_text_box')
                                @include('voyager::multilingual.input-hidden-bread-read')
                                {!! $dataTypeContent->{$row->field} !!}
                            @elseif($row->type == 'file')
                                @if(json_decode($dataTypeContent->{$row->field}))
                                    @foreach(json_decode($dataTypeContent->{$row->field}) as $file)
                                        <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}">
                                            {{ $file->original_name ?: '' }}
                                        </a>
                                        <br/>
                                    @endforeach
                                @else
                                    <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($row->field) ?: '' }}">
                                        {{ __('voyager::generic.download') }}
                                    </a>
                                @endif
                            @else
                                @include('voyager::multilingual.input-hidden-bread-read')
                                <p>{{ $dataTypeContent->{$row->field} }}</p>
                            @endif
                        </div><!-- panel-body -->
                        @if(!$loop->last)
                            <hr style="margin:0;">
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Single delete modal --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> {{ __('voyager::generic.delete_question') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}?</h4>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('voyager.'.$dataType->slug.'.index') }}" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm"
                               value="{{ __('voyager::generic.delete_confirm') }} {{ strtolower($dataType->getTranslatedAttribute('display_name_singular')) }}">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop

@section('javascript')
    // <script src="https://unpkg.com/chart.js@3"></script>
//     <script>
//         // Sales graph chart
//         var salesGraphChartCanvas = $('#line-chart').get(0).getContext('2d')
//         // $('#revenue-chart').get(0).getContext('2d');
//
//         var salesGraphChartData = {
//             labels: ['fdasfa Q1', '2011 Q2', '2011 Q3', '2011 Q4', '2012 Q1', '2012 Q2', '2012 Q3', '2012 Q4', '2013 Q1', '2013 Q2'],
//             datasets: [
//                 {
//                     label: 'Digital Goods',
//                     fill: false,
//                     borderWidth: 2,
//                     lineTension: 0,
//                     spanGaps: true,
//                     borderColor: '#efefef',
//                     pointRadius: 3,
//                     pointHoverRadius: 7,
//                     pointColor: '#efefef',
//                     pointBackgroundColor: '#efefef',
//                     data: [2666, 2778, 4912, 3767, 6810, 5670, 4820, 15073, 10687, 8432]
//                 }
//             ]
//         }
//
//         var salesGraphChartOptions = {
//             maintainAspectRatio: false,
//             responsive: true,
//             legend: {
//                 display: false
//             },
//             scales: {
//                 xAxes: [{
//                     ticks: {
//                         fontColor: '#efefef'
//                     },
//                     gridLines: {
//                         display: false,
//                         color: '#efefef',
//                         drawBorder: false
//                     }
//                 }],
//                 yAxes: [{
//                     ticks: {
//                         stepSize: 5000,
//                         fontColor: '#efefef'
//                     },
//                     gridLines: {
//                         display: true,
//                         color: '#efefef',
//                         drawBorder: false
//                     }
//                 }]
//             }
//         }
//
//         // This will get the first returned node in the jQuery collection.
//         // eslint-disable-next-line no-unused-vars
//         var salesGraphChart = new Chart(salesGraphChartCanvas, { // lgtm[js/unused-local-variable]
//             type: 'line',
//             data: salesGraphChartData,
//             options: salesGraphChartOptions
//         })
//     </script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://unpkg.com/chart.js@3"></script>
    <script src="https://unpkg.com/@sgratzl/chartjs-chart-boxplot@3"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="text/javascript" src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
    <script>
        var data = {!! json_encode($graphData) !!};

        var boxplotDataFromServer = {!! json_encode($boxpot_data) !!};
        var boxplotDataLabels = {!! json_encode($boxpot_data_labels) !!};
        let boxplot_cj_data = {!! json_encode($boxplot_cj_data) !!};

        function randomValues(count, min, max) {
            const delta = max - min;
            return Array.from({length: count}).map(() => Math.random() * delta + min);
        }

        console.log(boxplotDataLabels);
        const boxplotData = {
            // define label tree
            // labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            labels: boxplotDataLabels,
            datasets: [{
                label: 'Keyword Ranks Graph',
                backgroundColor: '#63a8ff',
                borderColor: '#63a8ff',
                borderWidth: 1,
                outlierColor: '#999999',
                padding: 10,
                itemRadius: 0,
                data: boxplotDataFromServer
            }]
        };
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                stacked: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Keyword Ranks Graph'
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            // label: function(tooltipItems) {
                            //     tooltipItems.label = tooltipItems.label + " Iteration"
                            //     console.log(tooltipItems);
                            //     // return tooltipItems.yLabel + ' : ' + tooltipItems.xLabel + " Files";
                            // }
                        }
                    },
                    datalabels: {
                        function (context) {
                            return context.chart.isDatasetVisible(context.datasetIndex);
                        }
                    },
                },
                scales: {

                }
            },
        };
        window.onload = () => {
            const speedCanvas = document.getElementById("rankChart");

            window.lineChart = new Chart(speedCanvas, config);

            $('#app').smartPanel();
        };


        function dataDisplay(event, label, id){
            console.log(id);
            window.lineChart.data.datasets.forEach(function(ds) {
                if(ds.label == label){

                    if(event.target.checked){
                        ds.hidden = false;
                    }else{
                        ds.hidden = true;
                    }
                    return false;
                }

            });
            window.lineChart.update();

            window.myBar.data.datasets[0].data.forEach(function(ds, key) {

                if(id == key){
                    if(event.target.checked){
                        // ds.hidden = false;
                        // return ds;
                        window.myBar.data.datasets[0].data.push(ds);
                    }else{
                        // window.myBar.data.datasets[0].data.push(ds);
                        // ds.hidden = true;
                    }
                    return false;
                }

            });
            // window.myBar.data.datasets[0].data.push([45,34,42,12,33,22,12,32]);
            // console.log(window.myBar.data);
            window.myBar.update();
        }

        $("#select").on('click', function() {
            $('.site').prop('checked', true);
            window.lineChart.data.datasets.forEach(function(ds) {
                ds.hidden = false;
            });
            window.lineChart.update();

            window.myBar.data.datasets.forEach(function(ds) {
                ds.hidden = false;
            });
            window.myBar.update();
        });

        $("#unselect").on('click',function() {
            $('.site').prop('checked', false);
            window.lineChart.data.datasets.forEach(function(ds) {
                ds.hidden = true;

            });
            window.lineChart.update();

            window.myBar.data.datasets.forEach(function(ds) {
                ds.hidden = true;

            });
            window.myBar.update();
        });

        function displayUrls(urls){
            $('#urls').html('');
            var html = '';
            $.each(urls, function(index, url){
                html += '<div style="width=100%"><a href="'+url+'">'+url+'</a></div><hr>'
            });
            $('#urls').html(html);
            $('#exampleModal').modal('show');
        }

        $("#analyze").on('click', function () {
            var urls = [];
            $.each($('input[class="site"]:checked'), function (index, checkbox) {
                urls.push(checkbox.value);
            });
            if(urls.length == 0){
                swal({
                    title: "Error",
                    text: "Please select any result first",
                    icon: "error",
                });
            }else{
                swal({
                    title: "Are you sure?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((confirm) => {
                        if (confirm) {
                            $('#selected_urls').val(JSON.stringify(urls));
                            HoldOn.open();
                            $('#analyze_form').submit();
                        }
                    });

            }
        });


    </script>


    @if ($isModelTranslatable)
        <script>
            $(document).ready(function () {
                $('.side-body').multilingual();
            });
        </script>
    @endif
    <script>
        var deleteFormAction;
        $('.delete').on('click', function (e) {
            var form = $('#delete_form')[0];

            if (!deleteFormAction) {
                // Save form action initial value
                deleteFormAction = form.action;
            }

            form.action = deleteFormAction.match(/\/[0-9]+$/)
                ? deleteFormAction.replace(/([0-9]+$)/, $(this).data('id'))
                : deleteFormAction + '/' + $(this).data('id');

            $('#delete_modal').modal('show');
        });

    </script>
@stop
