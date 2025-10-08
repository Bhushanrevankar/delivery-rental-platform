@extends('layouts.admin.app')

@section('title', translate('Ride Request Details'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">
                        <span class="page-header-icon">
                            <img src="{{ asset('/public/assets/admin/img/shopping-basket.png') }}" class="w--20"
                                alt="">
                        </span>
                        <span>
                            {{ translate('messages.ride_request_details') }}
                        </span>
                    </h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                        href="{{ route('admin.ride.request.show', ['id' => $rideRequest->id - 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="{{ translate('messages.previous_ride') }}">
                        <i class="tio-chevron-left"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                        href="{{ route('admin.ride.request.show', ['id' => $rideRequest->id + 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="{{ translate('messages.next_ride') }}">
                        <i class="tio-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 order-print-area-left">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header border-0 align-items-start flex-wrap">
                        <div class="order-invoice-left d-flex d-sm-block justify-content-between">
                            <div>
                                <h1 class="page-header-title">
                                    Order #100031
                                </h1>
                                <span class="mt-2 d-block">
                                    <i class="tio-date-range"></i>
                                    02 Oct 2022 03:24:pm
                                </span>
                                <div class="hs-unfold mt-1">

                                </div>
                            </div>
                            {{-- <div class="d-sm-none">
                                <a class="btn btn--primary print--btn font-regular" href="http://localhost/6ammart/admin/order/generate-invoice/100031">
                                    <i class="tio-print mr-sm-1"></i> <span>Print
                                        Invoice</span>
                                </a>
                            </div> --}}
                        </div>
                        <div class="order-invoice-right mt-3 mt-sm-0">
                            {{-- <div class="btn--container ml-auto align-items-center justify-content-end">
                                <a class="btn btn--primary print--btn font-regular d-none d-sm-block"
                                    href={{ route('admin.order.generate-invoice', [$rideRequest['id']]) }}>
                                    <i class="tio-print mr-sm-1"></i> <span>{{ translate('messages.print') }}
                                        {{ translate('messages.invoice') }}</span>
                                </a>
                            </div> --}}
                            <div class="text-right mt-3 order-invoice-right-contents text-capitalize">
                                <h5>
                                    <button
                                        class="btn order--details-btn-sm btn--primary btn-outline-primary btn--sm font-regular"
                                        data-toggle="modal" data-target="#locationModal"><i class="tio-poi"></i>
                                        Show locations on map</button>
                                    <div>
                                    </div>
                                </h5>
                                <h6>
                                    {{ translate('status') }} :
                                    @if ($rideRequest['ride_status'] == 'pending')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.pending') }}
                                        </span>
                                    @elseif($rideRequest['ride_status'] == 'accepted')
                                        <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.confirmed') }}
                                        </span>
                                    @elseif($rideRequest['ride_status'] == 'canceled')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.canceled') }}
                                        </span>
                                    @elseif($rideRequest['ride_status'] == 'picked_up')
                                        <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.out_for_delivery') }}
                                        </span>
                                    @elseif($rideRequest['ride_status'] == 'completed')
                                        <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.completed') }}
                                        </span>
                                    @elseif($rideRequest['ride_status'] == 'failed')
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate('messages.payment') }}
                                            {{ translate('messages.failed') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                            {{ translate(str_replace('_', ' ', $rideRequest['ride_status'])) }}
                                        </span>
                                    @endif
                                </h6>
                                <h6>
                                    {{ translate('messages.zone') }}:
                                    {{ $rideRequest->zone ? $rideRequest->zone->name : translate('messages.not_found') }}
                                </h6>

                                {{-- <h6 class="text-capitalize">
                                    {{ translate('messages.payment') }} {{ translate('messages.method') }} :
                                    {{ translate(str_replace('_', ' ', $rideRequest['payment_method'])) }}
                                </h6> --}}
                                {{-- <h6 class="">
                                    @if ($rideRequest['transaction_reference'] == null)
                                        {{ translate('messages.reference') }} {{ translate('messages.code') }} :
                                        <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                            data-target=".bd-example-modal-sm">
                                            {{ translate('messages.add') }}
                                        </button>
                                    @else
                                        {{ translate('messages.reference') }} {{ translate('messages.code') }} :
                                        {{ $rideRequest['transaction_reference'] }}
                                    @endif
                                </h6>
                                <h6 class="text-capitalize">{{ translate('messages.order') }}
                                    {{ translate('messages.type') }}
                                    : <label
                                        class="fz--10 badge badge-soft-primary">{{ translate(str_replace('_', ' ', $rideRequest['order_type'])) }}</label>
                                </h6>
                                <h6>
                                    {{ translate('payment_status') }} :
                                    @if ($rideRequest['payment_status'] == 'paid')
                                        <span class="badge badge-soft-success ml-sm-3">
                                            {{ translate('messages.paid') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{ translate('messages.unpaid') }}
                                        </span>
                                    @endif

                                </h6> --}}
                                @if ($rideRequest->order_attachment)
                                    <h5 class="text-dark">
                                        {{ translate('messages.prescription') }}:
                                    </h5>
                                    <button class="btn w-100 px-0" data-toggle="modal" data-target="#imagemodal"
                                        title="{{ translate('messages.order') }} {{ translate('messages.attachment') }}">
                                        <div class="gallary-card ml-auto">
                                            <img src="{{ asset('storage/app/' . 'public/order/' . $rideRequest->order_attachment) }}"
                                                alt="{{ translate('messages.prescription') }}"
                                                class="initial--22 object-cover">
                                        </div>
                                    </button>
                                    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog"
                                        aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        {{ translate('messages.prescription') }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"><span
                                                            aria-hidden="true">&times;</span><span
                                                            class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ asset('storage/app/' . 'public/order/' . $rideRequest->order_attachment) }}"
                                                        class="initial--22 w-100">
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-primary"
                                                        href="{{ route('admin.file-manager.download', base64_encode('public/order/' . $rideRequest->order_attachment)) }}"><i
                                                            class="tio-download"></i> {{ translate('messages.download') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table class="table table-bordered ride-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-bottom-0">{{ translate('messages.pick_up_address') }}</th>
                                        <th class="border-bottom-0">{{ translate('messages.destination') }}</th>
                                        <th class="border-bottom-0">{{ translate('messages.distance') }}</th>
                                        <th class="border-bottom-0">{{ translate('messages.time_duration') }}</th>
                                        <th class="border-bottom-0">{{ translate('messages.fare') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div style="max-width: 160px;">{{ $rideRequest->pickup_address }}</div>
                                        </td>
                                        <td>
                                            <div style="max-width: 160px;">{{ $rideRequest->dropoff_address }}</div>
                                        </td>
                                        <td>
                                            <div class="text-info"><strong>{{ translate('messages.estimated_distance') }} :
                                                </strong> {{ $rideRequest->estimated_distance }}
                                                {{ trans('messages.km') }}</div>
                                            <div class="text-title"><strong>{{ translate('messages.actual_distance') }} :
                                                </strong>
                                                {{ !in_array($rideRequest->ride_status, ['pending', 'accepted']) ? $rideRequest->actual_distance . ' ' . trans('messages.km') : translate('messages.on_going_ride') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-info"><strong>{{ translate('messages.estimated_time') }} :
                                                </strong> {{ $rideRequest->estimated_time }}
                                                {{ trans('messages.minutes') }}</div>
                                            <div class="text-title"><strong>{{ translate('messages.actual_time') }} :
                                                </strong>
                                                {{ !in_array($rideRequest->ride_status, ['pending', 'accepted']) ? $rideRequest->actual_time . ' ' . trans('messages.minutes') : translate('messages.on_going_ride') }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-info"><strong>{{ translate('messages.estimated_fare') }} :
                                                </strong>
                                                {{ \App\CentralLogics\Helpers::format_currency($rideRequest->estimated_fare) }}
                                            </div>
                                            <div class="text-title"><strong>{{ translate('messages.actual_fare') }} :
                                                </strong>
                                                {{ !in_array($rideRequest->ride_status, ['pending', 'accepted']) ? \App\CentralLogics\Helpers::format_currency($rideRequest->actual_fare) : trans('messages.on_going_ride') }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row justify-content-md-end mb-3 mt-4 mx-0">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-right">
                                    <dt class="col-6">{{ translate('messages.fare') }}:</dt>
                                    <dd class="col-6 pr-md-5">
                                        +
                                        {{ \App\CentralLogics\Helpers::format_currency($rideRequest->actual_fare ?? $rideRequest->estimated_fare) }}
                                    </dd>
                                    <dt class="col-6">{{ translate('messages.vat/tax') }}:</dt>
                                    <dd class="col-6 pr-md-5">
                                        + {{ \App\CentralLogics\Helpers::format_currency($rideRequest->tax ?? 0) }}
                                        <hr class="mb-0">
                                    </dd>
                                    {{-- <dt class="col-6">{{ translate('messages.coupon_discount') }}:</dt>
                                    <dd class="col-6 pr-md-5">
                                    + {{ \App\CentralLogics\Helpers::format_currency($rideRequest->tax??0) }}</dd> --}}
                                    <dt class="col-6">{{ translate('messages.total') }}:</dt>
                                    <dd class="col-6 pr-md-5">
                                        {{ \App\CentralLogics\Helpers::format_currency($rideRequest->total_fare ?? 0) }}
                                    </dd>
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4 order-print-area-right">
                {{-- <div class="card">
                    <div class="card-header justify-content-center">
                        <h5 class="card-title">{{ translate('order_setup') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($rideRequest->ride_status != 'refunded')
                            <div class="hs-unfold w-100">
                                <div class="dropdown">
                                    <button
                                        class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100"
                                        type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        {{ translate('messages.status') }}
                                    </button>
                                    <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item {{ $rideRequest['ride_status'] == 'pending' ? 'active' : '' }}"
                                            onclick="route_alert('{{ route('admin.order.status', ['id' => $rideRequest['id'], 'ride_status' => 'pending']) }}','{{ translate('Change status to pending ?') }}')"
                                            href="javascript:">{{ translate('messages.pending') }}</a>
                                        <a class="dropdown-item {{ $rideRequest['ride_status'] == 'confirmed' ? 'active' : '' }}"
                                            onclick="route_alert('{{ route('admin.order.status', ['id' => $rideRequest['id'], 'ride_status' => 'confirmed']) }}','{{ translate('Change status to confirmed ?') }}')"
                                            href="javascript:">{{ translate('messages.confirmed') }}</a>
                                        <a class="dropdown-item {{ $rideRequest['ride_status'] == 'processing' ? 'active' : '' }}"
                                            onclick="route_alert('{{ route('admin.order.status', ['id' => $rideRequest['id'], 'ride_status' => 'processing']) }}','{{ translate('Change status to processing ?') }}')"
                                            href="javascript:">{{ translate('messages.processing') }}</a>
                                        <a class="dropdown-item {{ $rideRequest['ride_status'] == 'handover' ? 'active' : '' }}"
                                            onclick="route_alert('{{ route('admin.order.status', ['id' => $rideRequest['id'], 'ride_status' => 'handover']) }}','{{ translate('Change status to handover ?') }}')"
                                            href="javascript:">{{ translate('messages.handover') }}</a>
                                        <a class="dropdown-item {{ $rideRequest['ride_status'] == 'picked_up' ? 'active' : '' }}"
                                            onclick="route_alert('{{ route('admin.order.status', ['id' => $rideRequest['id'], 'ride_status' => 'picked_up']) }}','{{ translate('Change status to out for delivery ?') }}')"
                                            href="javascript:">{{ translate('messages.out_for_delivery') }}</a>
                                        <a class="dropdown-item {{ $rideRequest['ride_status'] == 'delivered' ? 'active' : '' }}"
                                            onclick="route_alert('{{ route('admin.order.status', ['id' => $rideRequest['id'], 'ride_status' => 'delivered']) }}','{{ translate('Change status to delivered (payment status will be paid if not)?') }}')"
                                            href="javascript:">{{ translate('messages.delivered') }}</a>
                                        <a class="dropdown-item {{ $rideRequest['ride_status'] == 'canceled' ? 'active' : '' }}"
                                            onclick="route_alert('{{ route('admin.order.status', ['id' => $rideRequest['id'], 'ride_status' => 'canceled']) }}','{{ translate('Change status to canceled ?') }}')"
                                            href="javascript:">{{ translate('messages.canceled') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if (!$rideRequest->rider && $rideRequest['order_type'] != 'take_away' && (($rideRequest->store && !$rideRequest->store->self_delivery_system) || $parcel_order))
                            <div class="w-100 text-center mt-3">
                                <button type="button" class="btn btn--primary w-100" data-toggle="modal"
                                    data-target="#myModal" data-lat='21.03' data-lng='105.85'>
                                    {{ translate('messages.assign_delivery_mam_manually') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div> --}}

                @if ($rideRequest->rider)
                    <div class="card mt-2">
                        <div class="card-body">
                            <h5 class="card-title mb-3 d-flex flex-wrap align-items-center">
                                <span class="card-header-icon">
                                    <i class="tio-user"></i>
                                </span>
                                <span>{{ translate('messages.deliveryman') }}</span>
                                {{-- @if (!isset($rideRequest->delivered))
                                    <a type="button" href="#myModal" class="text--base cursor-pointer ml-auto"
                                        data-toggle="modal" data-target="#myModal">
                                        {{ translate('messages.change') }}
                                    </a>
                                @endif --}}
                            </h5>
                            <a class="media align-items-center deco-none customer--information-single"
                                href="{{ route('admin.delivery-man.preview', [$rideRequest->rider['id']]) }}">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img"
                                        onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'"
                                        src="{{ asset('storage/app/public/delivery-man/' . $rideRequest->rider->image) }}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span
                                        class="text-body d-block text-hover-primary mb-1">{{ $rideRequest->rider['f_name'] . ' ' . $rideRequest->rider['l_name'] }}</span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-shopping-basket-outlined mr-2"></i>
                                        {{ $rideRequest->rider->orders_count }}
                                        {{ translate('messages.orders_delivered') }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-call-talking-quiet mr-2"></i>
                                        {{ $rideRequest->rider['phone'] }}
                                    </span>

                                    <span class="text--title font-semibold d-flex align-items-center">
                                        <i class="tio-email-outlined mr-2"></i>
                                        {{ $rideRequest->rider['email'] }}
                                    </span>

                                </div>
                            </a>
                            <hr>
                            {{-- @php($address = $rideRequest->rider_last_location)
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ translate('messages.last') }} {{ translate('messages.location') }}</h5>
                            </div>
                            @if (isset($address))
                                <span class="d-block">
                                    <a target="_blank"
                                        href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}">
                                        <i class="tio-map"></i> {{ $address['location'] }}<br>
                                    </a>
                                </span>
                            @else
                                <span class="d-block text-lowercase qcont">
                                    {{ translate('messages.location') . ' ' . translate('messages.not_found') }}
                                </span>
                            @endif --}}
                        </div>
                    </div>
                @endif


                <div class="card mt-2">
                    <div class="card-body pt-3">
                        @if ($rideRequest->customer)
                            <h5 class="card-title mb-3">
                                <span class="card-header-icon">
                                    <i class="tio-user"></i>
                                </span>
                                <span>{{ translate('customer_information') }}</span>
                            </h5>

                            <a class="media align-items-center deco-none customer--information-single"
                                href="{{ route('admin.customer.view', [$rideRequest->customer['id']]) }}">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img"
                                        onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'"
                                        src="{{ asset('storage/app/public/profile/' . $rideRequest->customer->image) }}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                        {{ $rideRequest->customer['f_name'] . ' ' . $rideRequest->customer['l_name'] }}
                                    </span>
                                    <span>{{ $rideRequest->customer->orders_count }}
                                        {{ translate('messages.orders') }}</span>
                                    <span class="text--title font-semibold d-block">
                                        <i class="tio-call-talking-quiet mr-2"></i>{{ $rideRequest->customer['phone'] }}
                                    </span>
                                    <span class="text--title">
                                        <i class="tio-email mr-2"></i>{{ $rideRequest->customer['email'] }}
                                    </span>
                                </div>
                            </a>
                            @if ($rideRequest->receiver_details)
                                @php($receiver_details = $rideRequest->receiver_details)
                                <h5 class="card-title mt-3">
                                    <span class="card-header-icon">
                                        <i class="tio-user"></i>
                                    </span>
                                    <span>{{ translate('messages.receiver') }} {{ translate('messages.info') }}</span>
                                </h5>
                                @if (isset($receiver_details))
                                    <span class="delivery--information-single mt-3">
                                        <span class="name">{{ translate('messages.name') }}</span>
                                        <span class="info">{{ $receiver_details['contact_person_name'] }}</span>
                                        <span class="name">{{ translate('messages.contact') }}</span>
                                        <a class="deco-none info"
                                            href="tel:{{ $receiver_details['contact_person_number'] }}">
                                            {{ $receiver_details['contact_person_number'] }}</a>
                                        @if (isset($receiver_details['address']))
                                            @if (isset($receiver_details['latitude']) && isset($receiver_details['longitude']))
                                                <a class="mt-2" target="_blank"
                                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $receiver_details['latitude'] }}+{{ $receiver_details['longitude'] }}">
                                                    <i class="tio-poi"></i>{{ $receiver_details['address'] }}
                                                </a>
                                            @else
                                                <i class="tio-poi"></i>{{ $receiver_details['address'] }}
                                            @endif
                                        @endif
                                    </span>
                                @endif
                            @endif
                        @else
                            <span class="badge badge-soft-danger py-2 d-block qcont">
                                {{ translate('Customer Not found!') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Customer Card -->
            </div>
        </div>
        <!-- End Row -->
    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{ translate('messages.reference') }}
                        {{ translate('messages.code') }} {{ translate('messages.add') }}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                        aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{ route('admin.order.add-payment-ref-code', [$rideRequest['id']]) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <!-- Input Group -->
                        <div class="form-group">
                            <input type="text" name="transaction_reference" class="form-control"
                                placeholder="{{ translate('messages.Ex:') }} Code123" required>
                        </div>
                        <!-- End Input Group -->
                        <div class="text-right">
                            <button class="btn btn--primary">{{ translate('messages.submit') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Modal -->
@endsection

@push('script_2')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=places&v=3.45.8">
    </script>
    <script>
        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
    <script>
        var map = null;
        var myLatlng = new google.maps.LatLng({{ $rideRequest->pickup_point->latitude }},
            {{ $rideRequest->pickup_point->longitude }});
        var locationbounds = new google.maps.LatLngBounds(null);
        var dmMarkers = [];
        locationbounds.extend(myLatlng);
        var myOptions = {
            center: myLatlng,
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP,

            panControl: true,
            mapTypeControl: false,
            panControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            scaleControl: false,
            streetViewControl: false,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            }
        };

        function initializeGMap() {

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
            var infowindow = new google.maps.InfoWindow();
            var DropoffMarker = new google.maps.Marker({
                position: new google.maps.LatLng({{ $rideRequest->dropoff_point->latitude }},
                    {{ $rideRequest->dropoff_point->longitude }}),
                title: "{{ Str::limit($rideRequest->dropoff_address, 15, '...') }}",
                // icon: "{{ asset('public/assets/admin/img/restaurant_map.png') }}",
                map: map,

            });

            {{-- google.maps.event.addListener(DropoffMarker, 'click', (function(DropoffMarker) {
                return function() {
                    infowindow.setContent(
                        "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{ asset('storage/app/public/restaurant/' . $rideRequest->store->logo) }}'></div><div class='text-break' style='float:right; padding: 10px;'><b>{{ Str::limit($rideRequest->store->name, 15, '...') }}</b><br /> {{ $rideRequest->store->address }}</div>"
                        );
                    infowindow.open(map, DropoffMarker);
                }
            })(DropoffMarker));
            --}}

        }

        function initMap() {
            let map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: {
                    lat: {{ $rideRequest->pickup_point->latitude }},
                    lng: {{ $rideRequest->pickup_point->longitude }}
                }
            });

            let zonePolygon = null;

            //get current location block
            let infoWindow = new google.maps.InfoWindow();
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        myLatlng = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        infoWindow.setPosition(myLatlng);
                        infoWindow.setContent("Location found.");
                        infoWindow.open(map);
                        map.setCenter(myLatlng);
                    },
                    () => {
                        handleLocationError(true, infoWindow, map.getCenter());
                    }
                );
            } else {
                // Browser doesn't support Geolocation
                handleLocationError(false, infoWindow, map.getCenter());
            }
            //-----end block------
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            let markers = [];
            const bounds = new google.maps.LatLngBounds();
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    console.log(place.geometry.location);
                    if (!google.maps.geometry.poly.containsLocation(
                            place.geometry.location,
                            zonePolygon
                        )) {
                        toastr.error('{{ translate('messages.out_of_coverage') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        return false;
                    }

                    document.getElementById('latitude').value = place.geometry.location.lat();
                    document.getElementById('longitude').value = place.geometry.location.lng();

                    const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };
                    // Create a marker for each place.
                    markers.push(
                        new google.maps.Marker({
                            map,
                            icon,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }

        $(document).ready(function() {

            // Re-init map before show modal
            $('#myModal').on('shown.bs.modal', function(event) {
                initMap();
                var button = $(event.relatedTarget);
                $("#dmassign-map").css("width", "100%");
                $("#map_canvas").css("width", "100%");
            });

            // Trigger map resize event after modal shown
            $('#myModal').on('shown.bs.modal', function() {
                initializeGMap();
                google.maps.event.trigger(map, "resize");
                map.setCenter(myLatlng);
            });


            function initializegLocationMap() {
                map = new google.maps.Map(document.getElementById("location_map_canvas"), myOptions);

                var infowindow = new google.maps.InfoWindow();

                //Pickup location
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng({{ $rideRequest->pickup_point->latitude }},
                        {{ $rideRequest->pickup_point->longitude }}),
                    map: map,
                    title: "{{ $rideRequest->pickup_address }}",
                    // icon: "{{ asset('public/assets/admin/img/customer_location.png') }}"
                });

                {{-- // google.maps.event.addListener(marker, 'click', (function(marker) {
                //     return function() {
                //         infowindow.setContent(
                //             "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{ asset('storage/app/public/profile/' . $rideRequest->customer->image) }}'></div><div style='float:right; padding: 10px;'><b>{{ $rideRequest->customer->f_name }} {{ $rideRequest->customer->l_name }}</b><br />{{ $address['address'] }}</div>"
                //             );
                //         infowindow.open(map, marker);
                //     }
                // })(marker)); --}}
                locationbounds.extend(marker.getPosition());
                //Drop off location
                var Retaurantmarker = new google.maps.Marker({
                    position: new google.maps.LatLng({{ $rideRequest->dropoff_point->latitude }},
                        {{ $rideRequest->dropoff_point->longitude }}),
                    map: map,
                    title: "{{ Str::limit($rideRequest->dropoff_address, 15, '...') }}",
                    // icon: "{{ asset('public/assets/admin/img/restaurant_map.png') }}"
                });

                {{-- // google.maps.event.addListener(Retaurantmarker, 'click', (function(Retaurantmarker) {
                //     return function() {
                //         infowindow.setContent(
                //             "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{ asset('storage/app/public/restaurant/' . $rideRequest->store->logo) }}'></div> <div style='float:right; padding: 10px;'><b>{{ Str::limit($rideRequest->store->name, 15, '...') }}</b><br /> {{ $rideRequest->store->address }}</div>"
                //             );
                //         infowindow.open(map, Retaurantmarker);
                //     }
                // })(Retaurantmarker)); --}}
                locationbounds.extend(Retaurantmarker.getPosition());

                @if ($rideRequest->rider && $rideRequest->rider_last_location)
                    var dmmarker = new google.maps.Marker({
                        position: new google.maps.LatLng(
                            {{ $rideRequest->rider_last_location['latitude'] }},
                            {{ $rideRequest->rider_last_location['longitude'] }}),
                        map: map,
                        title: "{{ $rideRequest->rider->f_name }} {{ $rideRequest->rider->l_name }}",
                        icon: "{{ asset('public/assets/admin/img/delivery_boy_map.png') }}"
                    });

                    google.maps.event.addListener(dmmarker, 'click', (function(dmmarker) {
                        return function() {
                            infowindow.setContent(
                                "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{ asset('storage/app/public/delivery-man/' . $rideRequest->rider->image) }}'></div> <div style='float:right; padding: 10px;'><b>{{ $rideRequest->rider->f_name }} {{ $rideRequest->rider->l_name }}</b><br /> {{ $rideRequest->rider_last_location['location'] }}</div>"
                            );
                            infowindow.open(map, dmmarker);
                        }
                    })(dmmarker));
                    locationbounds.extend(dmmarker.getPosition());
                @endif

                google.maps.event.addListenerOnce(map, 'idle', function() {
                    map.fitBounds(locationbounds);
                });
            }

            // Re-init map before show modal
            $('#locationModal').on('shown.bs.modal', function(event) {
                initializegLocationMap();
            });


            $('.dm_list').on('click', function() {
                var id = $(this).data('id');
                map.panTo(dmMarkers[id].getPosition());
                map.setZoom(13);
                dmMarkers[id].setAnimation(google.maps.Animation.BOUNCE);
                window.setTimeout(() => {
                    dmMarkers[id].setAnimation(null);
                }, 3);
            });
        })
    </script>
@endpush
