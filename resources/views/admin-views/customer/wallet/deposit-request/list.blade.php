@extends('layouts.admin.app')

@section('title', __('messages.customer') . ' ' . __('messages.deposit_requests'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title mr-3">
                <span class="page-header-icon">
                    <img src="{{ asset('/public/assets/admin/img/wallet.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ __('messages.customer') }} {{ __('messages.deposit_requests') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-filter-outlined"></i>
                    </span>
                    <span>{{ __('messages.filter') }} {{ __('messages.options') }}</span>
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.customer.wallet.deposit-requests.list') }}" method="get">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="from">{{ __('messages.from') }}</label>
                            <input type="date" name="from" id="from_date" value="{{ request()->get('from') }}"
                                class="form-control" title="{{ __('messages.from') }} {{ __('messages.date') }}">

                        </div>
                        <div class="col-sm-6">
                            <label for="to">{{ __('messages.to') }}</label>
                            <input type="date" name="to" id="to_date" value="{{ request()->get('to') }}"
                                class="form-control" title="{{ ucfirst(__('messages.to')) }} {{ __('messages.date') }}">
                        </div>

                        <div class="col-sm-6">
                            <select id='customer' name="customer_id"
                                data-placeholder="{{ __('messages.select_customer') }}"
                                class="js-data-example-ajax form-control" title="{{ __('messages.select_customer') }}">
                                @if (request()->get('customer_id') && ($customer_info = \App\Models\User::find(request()->get('customer_id'))))
                                    <option value="{{ $customer_info->id }}" selected>
                                        {{ $customer_info->f_name . ' ' . $customer_info->l_name }}({{ $customer_info->phone }})
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ __('messages.reset') }}</button>
                                <button type="submit" class="btn btn--primary"><i
                                        class="tio-filter-list mr-1"></i>{{ __('messages.filter') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <!-- Card -->
        <div class="card mt-3">
            <!-- Header -->
            <div class="card-header border-0">
                <h4 class="card-title">
                    {{-- <span class="card-header-icon">
                        <i class="tio-dollar-outlined"></i>
                    </span> --}}
                    <span>{{ __('messages.deposit_requests') }}</span>
                </h4>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable" class="table table-thead-bordered table-align-middle card-table table-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ __('messages.id') }}</th>
                                <th class="border-0">{{ __('messages.user') }} {{ __('messages.name') }}</th>
                                <th class="border-0">{{ __('messages.amount') }}</th>
                                <th class="border-0">{{ __('messages.payment_method') }}</th>
                                <th class="border-0">{{ __('messages.status') }}</th>
                                <th class="border-0">{{ __('messages.created_at') }}</th>
                                <th class="border-0">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($depositRequests as $k => $depositRequest)
                                <tr scope="row">
                                    <td>{{ $depositRequest->id }}</td>
                                    <td>
                                        <a
                                            href="{{ route('admin.customer.view', ['user_id' => $depositRequest->owner->id]) }}">
                                            {{ $depositRequest->owner->f_name }} {{ $depositRequest->owner->l_name }}
                                        </a>
                                    </td>
                                    <td>{{ $depositRequest->amount }}</td>
                                    <td>{{ $depositRequest->payment_channel }}</td>
                                    <td>
                                        <span
                                            @if ($depositRequest->status == 'rejected' || $depositRequest->status == 'canceled') class="badge badge-soft-danger"
                                            @elseif ($depositRequest->status == 'approved') class="badge badge-soft-primary"
                                            @else 
                                            class="badge badge-soft-warning" @endif>{{ ucwords($depositRequest->status) }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="d-block">{{ date('d-m-Y', strtotime($depositRequest->created_at)) }}</span>
                                        <span
                                            class="d-block">{{ date(config('timeformat'), strtotime($depositRequest->created_at)) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="btn action-btn btn--warning btn-outline-warning"
                                                href="{{ route('admin.customer.wallet.deposit-requests.details', [$depositRequest->id]) }}"
                                                title="{{ __('messages.view') }} {{ __('messages.deposit_request') }}"><i
                                                    class="tio-visible-outlined"></i>
                                            </a>
                                            {{-- <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:"
                                                onclick="form_alert('deposit-request-{{ $depositRequest->id }}','{{ translate('You want to remove this deposit request') }}')"
                                                title="{{ translate('messages.delete') }} {{ translate('messages.deposit_request') }}"><i
                                                    class="tio-delete-outlined"></i>
                                            </a>
                                            <form
                                                action="{{ route('admin.customer.wallet.deposit-requests.delete', [$depositRequest->id]) }}"
                                                method="post" id="deposit-request-{{ $depositRequest->id }}">
                                                @csrf @method('delete')
                                            </form> --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Body -->
            @if (count($depositRequests) !== 0)
                <hr>
            @endif
            <div class="page-area">
                {{-- {!! $transactions->withQueryString()->links() !!} --}}
            </div>
            @if (count($depositRequests) === 0)
                <div class="empty--data">
                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
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
    <script src="{{ asset('public/assets/admin') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('public/assets/admin') }}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="{{ asset('public/assets/admin') }}/js/hs.chartjs-matrix.js"></script>

    <script>
        $(document).on('ready', function() {
            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{ route('admin.customer.select-list') }}',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            all: true,
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    __port: function(params, success, failure) {
                        var $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });

            // INITIALIZATION OF FLATPICKR
            // =======================================================
            $('.js-flatpickr').each(function() {
                $.HSCore.components.HSFlatpickr.init($(this));
            });


            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function() {
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
                $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format(
                    'MMM D') + ' - ' + end.format('MMM D, YYYY'));
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            }, cb);

            cb(start, end);


            // INITIALIZATION OF CHARTJS
            // =======================================================
            $('.js-chart').each(function() {
                $.HSCore.components.HSChartJS.init($(this));
            });

            var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

            // Call when tab is clicked
            $('[data-toggle="chart"]').click(function(e) {
                let keyDataset = $(e.currentTarget).attr('data-datasets')

                // Update datasets for chart
                updatingChart.data.datasets.forEach(function(dataset, key) {
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
                        width: function(ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.right - a.left) / 70;
                        },
                        height: function(ctx) {
                            var a = ctx.chart.chartArea;
                            return (a.bottom - a.top) / 10;
                        }
                    }]
                },
                options: {
                    tooltips: {
                        callbacks: {
                            title: function() {
                                return '';
                            },
                            label: function(item, data) {
                                var v = data.datasets[item.datasetIndex].data[item.index];

                                if (v.v.toFixed() > 0) {
                                    return '<span class="font-weight-bold">' + v.v.toFixed() +
                                        ' hours</span> on ' + v.d;
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
            $('.js-clipboard').each(function() {
                var clipboard = $.HSCore.components.HSClipboard.init(this);
            });


            // INITIALIZATION OF CIRCLES
            // =======================================================
            $('.js-circle').each(function() {
                var circle = $.HSCore.components.HSCircles.init($(this));
            });
        });
    </script>

    <script>
        $('#from_date,#to_date').change(function() {
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
