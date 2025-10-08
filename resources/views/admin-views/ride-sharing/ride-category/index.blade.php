@extends('layouts.admin.app')

@section('title', translate('Ride Category'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/attribute.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('Add New Ride Category') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.ride.vehicle-category.store') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.name') }}</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="{{ translate('messages.ex_:_scooty') }}" maxlength="191" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{ translate('messages.vehicle_type') }}</label>
                                    <select name="dm_vehicle_id" class="form-control js-select2-custom" required>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{$vehicle->id}}">{{$vehicle->type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.base_fare') }}
                                            ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                        <input type="number" step=".01" name="base_fare" class="form-control"
                                            placeholder="10" min="0" max="9999999999.99" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.per_km_fare') }}
                                            ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                        <input type="number" step=".01" name="per_km_fare" class="form-control"
                                            placeholder="4" min="0" max="9999999999.99" required>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{ translate('messages.per_min_waiting_fare') }}
                                            ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                        <input type="number" step=".01" name="per_min_waiting_fare"
                                            class="form-control" placeholder="2" min="0" max="9999999999.99"
                                            required>
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
                                {{ translate('Ride Category') }} {{ translate('messages.list') }}<span
                                    class="badge badge-soft-dark ml-2"
                                    id="itemCount">{{ $vehicle_categories->total() }}</span>
                            </h5>
                            <form action="{{ route('admin.ride.vehicle-category.index') }}" id="search-form" method="GET"
                                class="search-form">
                                <!-- Search -->
                                <div class="input-group input--group">
                                    <input id="datatableSearch_" type="search" name="key" class="form-control"
                                        value="{{ request('key') }}"
                                        placeholder="{{ translate('ex_:_vehicle_category_name') }}"
                                        aria-label="{{ translate('messages.search') }}">
                                    <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                                </div>
                                <!-- End Search -->
                            </form>
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
                                    <th class="border-0">{{ translate('messages.name') }}</th>
                                    <th class="border-0">{{ translate('messages.vehicle_type') }}</th>
                                    <th class="border-0">{{ translate('messages.base_fare') }}</th>
                                    <th class="border-0">{{ translate('messages.per_km_fare') }}</th>
                                    <th class="border-0">{{ translate('messages.per_min_waiting_fare') }}</th>
                                    <th class="border-0">{{ translate('messages.status') }}</th>
                                    <th class="border-0">{{ translate('messages.action') }}</th>
                                </tr>

                            </thead>

                            <tbody id="set-rows">
                                @foreach ($vehicle_categories as $key => $vehicle_category)
                                    <tr>
                                        <td class="text-center">
                                            <span class="mr-3">
                                                {{ $key + $vehicle_categories->firstItem() }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-size-sm text-body mr-3">
                                                {{ Str::limit($vehicle_category['name'], 20, '...') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="font-size-sm text-body mr-3">
                                                {{ Str::limit($vehicle_category?->dmVehicle?->type, 20, '...') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ \App\CentralLogics\Helpers::format_currency($vehicle_category->base_fare) }}
                                        </td>
                                        <td class="text-center">
                                            {{ \App\CentralLogics\Helpers::format_currency($vehicle_category->per_km_fare) }}
                                        </td>
                                        <td class="text-center">
                                            {{ \App\CentralLogics\Helpers::format_currency($vehicle_category->per_min_waiting_fare) }}
                                        </td>
                                        <td class="text-center">
                                            <label class="toggle-switch toggle-switch-sm"
                                                for="stocksCheckbox{{ $vehicle_category->id }}">
                                                <input type="checkbox"
                                                    onclick="location.href='{{ route('admin.ride.vehicle-category.status', ['vehicle_category' => $vehicle_category->id, 'status' => $vehicle_category->status ? 0 : 1]) }}'"class="toggle-switch-input"
                                                    id="stocksCheckbox{{ $vehicle_category->id }}"
                                                    {{ $vehicle_category->status ? 'checked' : '' }}>
                                                <span class="toggle-switch-label mx-auto">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="btn action-btn btn--primary btn-outline-primary"
                                                    href="{{ route('admin.ride.vehicle-category.edit', ['vehicle_category' => $vehicle_category->id]) }}"
                                                    title="{{ translate('messages.edit') }}"><i class="tio-edit"></i></a>
                                                <a class="btn action-btn btn--danger btn-outline-danger form-alert"
                                                    href="javascript:"
                                                    data-id="vehicle-category-{{ $vehicle_category->id }}"
                                                    data-message="{{ translate('Want to delete this vehicle category ?') }}"
                                                    title="{{ translate('messages.delete') }}"><i
                                                        class="tio-delete-outlined"></i></a>
                                                <form
                                                    action="{{ route('admin.ride.vehicle-category.destroy', ['vehicle_category' => $vehicle_category['id']]) }}"
                                                    method="post" id="vehicle-category-{{ $vehicle_category['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if (count($vehicle_categories) !== 0)
                            <hr>
                        @endif
                        <div class="page-area">
                            {!! $vehicle_categories->links() !!}
                        </div>
                        @if (count($vehicle_categories) === 0)
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
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

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
