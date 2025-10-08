@extends('layouts.admin.app')

@section('title',translate('messages.ride_sharing_report'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/report.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{translate('messages.ride_sharing_report')}} <span class="mb-0 h6 badge badge-soft-success ml-2" id="itemCount">( {{$from}} - {{$to}} )</span>
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-12">
                        <div class="report-card-inner mb-3 pt-3">
                            <form action="{{route('admin.report.set-date')}}" method="post">
                                @csrf
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
                                    <h5 class="form-label m-0 mb-2 mr-2">
                                        {{translate('messages.show')}} {{translate('messages.data')}} by {{translate('messages.date')}}
                                        {{translate('messages.range')}}
                                    </h5>
                                    <button type="submit" class="btn btn--primary mb-2">{{translate('show_data')}}</button>
                                </div>
                                <div class="row g-3">
                                    <div class="col-sm-4" data-toggle="tooltip" data-placement="top" title="{{translate('messages.select_ride_sharing_zone')}}">
                                        <select name="zone_id" class="form-control js-select2-custom"
                                                onchange="set_filter('{{url()->full()}}',this.value, 'zone_id')">
                                            <option value="" {{!isset($zone)?'selected':''}}>{{translate('messages.all')}} {{translate('messages.zone')}}</option>
                                            @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                                                <option
                                                    value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                                                    {{$z['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="date" name="from" id="from_date" value="{{$from}}"
                                                class="form-control" required data-toggle="tooltip" data-placement="top" title="{{translate('messages.from_date')}}">
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="date" name="to" id="to_date" value="{{$to}}"
                                                class="form-control" required  data-toggle="tooltip" data-placement="top" title="{{translate('messages.to_date')}}">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--Admin earned-->
                    <div class="col-sm-6 col-md-4">
                        <!-- Card -->
                        <div class="resturant-card resturant-card-2 card--bg-1">
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($data['admin_earned'])}}</h4>
                            <span class="subtitle">{{translate('messages.admin')}} {{translate('messages.earned')}}</span>
                            <img class="resturant-icon" src="{{asset('public/assets/admin/img/report/earned.png')}}" alt="order-report">
                        </div>
                        <!-- End Card -->
                    </div>
                    <!--Admin earned End-->

                    <!--Rider earned-->
                    <div class="col-sm-6 col-md-4">
                    <!-- Card -->
                        <div class="resturant-card resturant-card-2 card--bg-3">
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($data['rider_earned'])}}</h4>
                            <span class="subtitle">{{translate('messages.rider_earned')}}</span>
                            <img class="resturant-icon" src="{{asset('public/assets/admin/img/report/delivery-fee.png')}}" alt="order-report">
                        </div>
                    <!-- End Card -->
                    </div>
                    <!--Rider earned end-->
                    <!--Total sell-->
                    <div class="col-sm-6 col-md-4">
                    <!-- Card -->
                        <div class="resturant-card resturant-card-2 card--bg-4">
                            <h4 class="title">{{\App\CentralLogics\Helpers::format_currency($data['total_sell'])}}</h4>
                            <span class="subtitle">{{translate('messages.total_sell')}}</span>
                            <img class="resturant-icon" src="{{asset('public/assets/admin/img/report/sell.png')}}" alt="order-report">
                        </div>
                    <!-- End Card -->
                    </div>
                    <!--total sell end-->

                    <!--In progress-->
                    <div class="col-sm-6 col-md-4">
                        <div class="resturant-card resturant-card-2 card--bg-4">
                            <h6 class="title">
                                {{$data['status_count']['in_progress']}}
                            </h6>
                            <span class="subtitle text-capitalize">
                                {{translate('messages.in_progress')}}
                            </span>
                            <img src="{{asset('public/assets/admin/img/report/progress.png')}}" alt="dashboard" class="resturant-icon">
                        </div>
                    </div>
                    <!--In progress End-->
                    <!--Canceled-->
                    <div class="col-sm-6 col-md-4">
                        <div class="resturant-card resturant-card-2 card--bg-1">
                            <h6 class="title">
                                {{isset($data['status_count']['canceled'])?$data['status_count']['canceled']:0}}
                            </h6>
                            <span class="subtitle text-capitalize">
                                {{translate('messages.canceled')}}
                            </span>
                            <img src="{{asset('public/assets/admin/img/report/canceled.png')}}" alt="dashboard" class="resturant-icon">
                        </div>
                    </div>
                    <!--canceled End-->
                    <!--In Delivered -->
                    <div class="col-sm-6 col-md-4">
                        <div class="resturant-card resturant-card-2 card--bg-3">
                            <h6 class="title">
                                {{isset($data['status_count']['completed'])?$data['status_count']['completed']:0}}
                            </h6>
                            <span class="subtitle text-capitalize">
                                {{translate('messages.completed')}}
                            </span>
                            <img src="{{asset('public/assets/admin/img/report/delivered.png')}}" alt="dashboard" class="resturant-icon">
                        </div>
                    </div>
                    <!--Delivered End-->

                </div>
            </div>
        </div>

        <!-- End Stats -->
        <!-- Card -->
        <div class="card mt-3">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper">
                    <h3 class="card-title">
                        {{translate('messages.transactions')}} <span class="badge badge-soft-secondary" id="countItems">{{ $transactions->total() }}</span>
                    </h3>
                    {{-- <form action="javascript:" id="search-form" class="search-form">
                        <!-- Search -->
                        <div class="input--group input-group input-group-merge input-group-flush">
                            <input class="form-control" placeholder="{{ translate('Search by Order ID') }}">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- Static Export Button -->
                    <div class="hs-unfold ml-3">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle btn export-btn btn-outline-primary btn--primary font--sm" href="javascript:;" data-hs-unfold-options="{
                                &quot;target&quot;: &quot;#usersExportDropdown&quot;,
                                &quot;type&quot;: &quot;css-animation&quot;
                            }" data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                            <i class="tio-download-to mr-1"></i> {{translate('export')}}
                        </a>

                        <div id="usersExportDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-reverse-y hs-unfold-hidden">

                            <span class="dropdown-header">{{translate('download_options')}}</span>
                            <a id="export-excel" class="dropdown-item" href="{{route('admin.report.day-wise-report-export', ['type'=>'excel',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="{{asset('public/assets/admin/svg/components/excel.svg')}}" alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="{{route('admin.report.day-wise-report-export', ['type'=>'csv',request()->getQueryString()])}}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2" src="{{asset('public/assets/admin/svg/components/placeholder-csv-format.svg')}}" alt="Image Description">
                                .{{ translate('messages.csv') }}
                            </a>

                        </div>
                    </div>
                    <!-- Static Export Button --> --}}
                </div>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-thead-bordered table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('sl')}}</th>
                                <th class="border-0">{{translate('messages.ride_request_id')}}</th>
                                <th class="border-0">{{translate('messages.total_fare')}}</th>
                                <th class="border-0">{{translate('messages.admin_commission')}}</th>
                                <th class="border-0">{{translate('messages.rider_commission')}}</th>
                                <th class="border-0">{{translate('messages.vat/tax')}}</th>
                                <th class="border-0">{{translate('messages.created_at')}}</th>
                            </tr>
                        </thead>
                        <tbody id="set-rows">
                        @foreach($transactions as $k=>$ot)
                            <tr scope="row">
                                <td >{{$k+$transactions->firstItem()}}</td>
                                <td><a href="{{route('admin.ride.request.show',['id'=>$ot->ride_request_id])}}">{{$ot->ride_request_id}}</a></td>
                                <td>{{\App\CentralLogics\Helpers::format_currency($ot->total_fare)}}</td>
                                <td>{{\App\CentralLogics\Helpers::format_currency($ot->admin_commission)}}</td>
                                <td>{{\App\CentralLogics\Helpers::format_currency($ot->rider_commission)}}</td>
                                <td>{{\App\CentralLogics\Helpers::format_currency($ot->tax)}}</td>
                                <td>{{$ot->created_at->format('Y/m/d '.config('timeformat'))}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Body -->
            @if(count($transactions) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $transactions->links() !!}
            </div>
            @if(count($transactions) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script')

@endpush

@push('script_2')

    <script src="{{asset('public/assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script
        src="{{asset('public/assets/admin')}}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="{{asset('public/assets/admin')}}/js/hs.chartjs-matrix.js"></script>

    <script>
        $(document).on('ready', function () {

            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function () {
                $.HSCore.components.HSFlatpickr.init($(this));
            });


            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function () {
                new HsNavScroller($(this)).init()
            });


            // INITIALIZATION OF DATERANGEPICKER
            // =======================================================
            $('.js-daterangepicker').daterangepicker();

            $('.js-daterangepicker-times').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'M/DD hh:mm A'
                }
            });

            var start = moment();
            var end = moment();

            function cb(start, end) {
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
            }

            $('#js-daterangepicker-predefined').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


            // INITIALIZATION OF CHARTJS
            // =======================================================
            $('.js-chart').each(function () {
                $.HSCore.components.HSChartJS.init($(this));
            });

            var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

            // Call when tab is clicked
            $('[data-toggle="chart"]').click(function (e) {
                let keyDataset = $(e.currentTarget).attr('data-datasets')

                // Update datasets for chart
                updatingChart.data.datasets.forEach(function (dataset, key) {
                    dataset.data = updatingChartDatasets[keyDataset][key];
                });
                updatingChart.update();
            })


            // INITIALIZATION OF MATRIX CHARTJS WITH CHARTJS MATRIX PLUGIN
            // =======================================================
            function generateHoursData() {
                var data = [];
                var dt = moment().subtract(365, 'days').startOf('day');
                var end = moment().startOf('day');
                while (dt <= end) {
                    data.push({
                        x: dt.format('YYYY-MM-DD'),
                        y: dt.format('e'),
                        d: dt.format('YYYY-MM-DD'),
                        v: Math.random() * 24
                    });
                    dt = dt.add(1, 'day');
                }
                return data;
            }

            $.HSCore.components.HSChartMatrixJS.init($('.js-chart-matrix'), {
                data: {
                    datasets: [{
                        label: 'Commits',
                        data: generateHoursData(),
                        width: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.right - a.left) / 70;
                        },
                        height: function (ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.bottom - a.top) / 10;
                        }
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            title: function () {
                                return '';
                            },
                            label: function (item, data) {
                                var v = data.datasets[item.datasetIndex].data[item.index];

                                if (v.v.toFixed() > 0) {
                                    return '<span class="font-weight-bold">' + v.v.toFixed() + ' hours</span> on ' + v.d;
                                } else {
                                    return '<span class="font-weight-bold">No time</span> on ' + v.d;
                                }
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            position: 'bottom',
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'week',
                                round: 'week',
                                displayFormats: {
                                    week: 'MMM'
                                }
                            },
                            ticks: {
                                "labelOffset": 20,
                                "maxRotation": 0,
                                "minRotation": 0,
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 12,
                            },
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            type: 'time',
                            offset: true,
                            time: {
                                unit: 'day',
                                parser: 'e',
                                displayFormats: {
                                    day: 'ddd'
                                }
                            },
                            ticks: {
                                "fontSize": 12,
                                "fontColor": "rgba(22, 52, 90, 0.5)",
                                "maxTicksLimit": 2,
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });


            // INITIALIZATION OF CLIPBOARD
            // =======================================================
            $('.js-clipboard').each(function () {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });


            // INITIALIZATION OF CIRCLES
            // =======================================================
            $('.js-circle').each(function () {
                var circle = $.HSCore.components.HSCircles.init($(this));
            });
        });
    </script>

    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('Invalid date range!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush
