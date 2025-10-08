@extends('layouts.admin.app')

@section('title', translate('messages.prepaid_card'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <i class="tio-credit-card nav-icon"></i>
                </span>
                <span>
                    {{ translate('messages.generate_prepaid_cards') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.users.delivery-man.prepaid-cards.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label" for="price">{{ translate('messages.price') }}
                                            ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                        <input id="price" type="number" step=".01" name="price"
                                            class="form-control" placeholder="10.00" min="0" max="9999999999.99"
                                            required>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="duration_days">{{ translate('messages.duration_days') }}
                                        </label>
                                        <input id="duration_days" type="number" name="duration_days" class="form-control"
                                            placeholder="4" min="1" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label" for="count">{{ translate('messages.count') }}
                                        </label>
                                        <input id="count" type="number" name="count" class="form-control"
                                            placeholder="2" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                <button type="submit" class="btn btn--primary">{{ translate('messages.submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header py-2 border-0">
                        <div class="search--button-wrapper">
                            <h5 class="card-title">
                                {{ translate('messages.prepaid_cards') }}<span class="badge badge-soft-dark ml-2"
                                    id="itemCount">{{ $prepaid_cards->total() }}</span>
                            </h5>
                            <form action="{{ route('admin.users.delivery-man.prepaid-cards') }}" id="search-form"
                                method="GET" class="search-form">
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch_" type="search" name="card_no" class="form-control"
                                        placeholder="Enter Card No." value="{{ request('card_no') }}"
                                        aria-label="{{ translate('messages.search') }}">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>

                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px"
                                    href="javascript:;"
                                    data-hs-unfold-options='{
                                "target": "#usersExportDropdown",
                                "type": "css-animation"
                            }'>
                                    <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                                </a>

                                <div id="usersExportDropdown"
                                    class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                    <span class="dropdown-header">{{ translate('messages.options') }}</span>
                                    <a id="export-copy" class="dropdown-item" href="javascript:;"
                                        title="{{ translate('messages.current_page_only') }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/illustrations/copy.svg"
                                            alt="Image Description">
                                        {{ translate('messages.copy') }}
                                    </a>
                                    <a id="export-print" class="dropdown-item" href="javascript:;"
                                        title="{{ translate('messages.current_page_only') }}">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/illustrations/print.svg"
                                            alt="Image Description">
                                        {{ translate('messages.print') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <span class="dropdown-header">{{ translate('messages.download') }}
                                        {{ translate('messages.options') }}</span>
                                    <a id="export-excel" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                            alt="Image Description">
                                        {{ translate('messages.excel') }}
                                    </a>
                                    <a id="export-csv" class="dropdown-item" href="javascript:;">
                                        <img class="avatar avatar-xss avatar-4by3 mr-2"
                                            src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                            alt="Image Description">
                                        .{{ translate('messages.csv') }}
                                    </a>
                                    <!-- <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                                                                                                                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                                                                                                                    src="{{ asset('public/assets/admin') }}/svg/components/pdf.svg"
                                                                                                                                                    alt="Image Description">
                                                                                                                                            {{ translate('messages.pdf') }}
                                                                                                                                        </a> -->
                                </div>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th class="border-0">{{ translate('sl') }}</th>
                                    <th class="border-0">{{ translate('messages.card_no') }}</th>
                                    <th class="border-0">{{ translate('messages.price') }}</th>
                                    <th class="border-0">{{ translate('messages.duration_days') }}</th>
                                    <th class="border-0">{{ translate('messages.pin') }}</th>
                                    <th class="border-0">{{ translate('messages.used') }}</th>
                                    <th class="border-0">{{ translate('messages.used_by') }}</th>
                                    <th class="border-0">{{ translate('messages.action') }}</th>
                                </tr>

                            </thead>

                            <tbody id="set-rows">
                                @foreach ($prepaid_cards as $key => $prepaid_card)
                                    <tr>
                                        <td class="text-center">
                                            <span class="mr-3">
                                                {{ $key + $prepaid_cards->firstItem() }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-size-sm text-body mr-3">
                                                {{ $prepaid_card->card_no }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ \App\CentralLogics\Helpers::format_currency($prepaid_card->price) }}
                                        </td>
                                        <td class="text-center">{{ $prepaid_card->duration_days }}</td>
                                        <td class="text-center">{{ $prepaid_card->pin }}</td>
                                        <td class="text-center">{{ $prepaid_card->is_used ? 'Yes' : 'No' }}</td>
                                        <td class="text-center">
                                            @if ($prepaid_card->usable instanceof App\Models\DeliveryMan)
                                                <a
                                                    href="{{ route('admin.users.delivery-man.preview', ['id' => $prepaid_card->usable->id]) }}">
                                                    {{ $prepaid_card->usable->f_name }}
                                                    {{ $prepaid_card->usable->l_name }}
                                                </a>
                                            @else
                                                None
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="btn action-btn btn--danger btn-outline-danger"
                                                    href="javascript:"
                                                    onclick="form_alert('attribute-{{ $prepaid_card->id }}','{{ translate('Want to delete this card?') }}')"
                                                    title="{{ translate('messages.delete') }}"><i
                                                        class="tio-delete-outlined"></i></a>
                                                <form
                                                    action="{{ route('admin.users.delivery-man.prepaid-cards.destroy', [$prepaid_card->id]) }}"
                                                    method="post" id="attribute-{{ $prepaid_card->id }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if (count($prepaid_cards) !== 0)
                            <hr>
                        @endif
                        <div class="page-area">
                            {!! $prepaid_cards->links() !!}
                        </div>
                        @if (count($prepaid_cards) === 0)
                            <div class="empty--data">
                                <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}"
                                    alt="public">
                                <h5>{{ translate('no_data_found') }}</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
                buttons: [{
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none',
                        action: function(e, dt, node, config) {
                            window.location.href =
                                '{{ route('admin.users.delivery-man.prepaid-cards.export', ['format' => 'xlsx']) }}';
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'd-none',
                        action: function(e, dt, node, config) {
                            window.location.href =
                                '{{ route('admin.users.delivery-man.prepaid-cards.export', ['format' => 'csv']) }}';
                        }
                    },
                    // {
                    //     extend: 'pdf',
                    //     className: 'd-none'
                    // },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
            });

            $('#export-copy').click(function() {
                datatable.button('.buttons-copy').trigger()
            });

            $('#export-excel').click(function() {
                datatable.button('.buttons-excel').trigger()
            });

            $('#export-csv').click(function() {
                datatable.button('.buttons-csv').trigger()
            });

            $('#export-print').click(function() {
                datatable.button('.buttons-print').trigger()
            });

            $('#column1_search').on('keyup', function() {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function() {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush
