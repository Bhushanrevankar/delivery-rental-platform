@extends('layouts.admin.app')

@section('title', translate('Deposit Request Details'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-print-none pb-3">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title mb-0">
                        {{ translate('messages.deposit_request') }} {{ translate('messages.details') }}
                    </h1>
                </div>

                {{-- <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                        href="{{ route('admin.customer.view', [$customer['id'] - 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Previous customer">
                        <i class="tio-arrow-backward"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                        href="{{ route('admin.customer.view', [$customer['id'] + 1]) }}" data-toggle="tooltip"
                        data-placement="top" title="Next customer">
                        <i class="tio-arrow-forward"></i>
                    </a>
                </div> --}}
            </div>
        </div>

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card">
                    <!-- Header -->
                    <div class="card-header border-0 align-items-start flex-wrap">
                        <div class="order-invoice-left d-flex d-sm-block justify-content-between">
                            <div>
                                <h1 class="page-header-title">
                                    {{ translate('messages.deposit_request') }} #{{ $depositRequest->id }}
                                </h1>
                                <span class="mt-2 d-block">
                                    <i class="tio-date-range"></i>
                                    {{ date('d M Y ' . config('timeformat'), strtotime($depositRequest->created_at)) }}
                                </span>

                                <h6 class="mt-2 pt-1 mb-2">
                                    {{ translate('messages.account_name') }} : {{ $depositRequest->account_name }}
                                </h6>

                                <h6 class="text-capitalize">
                                    {{ translate('messages.account_number') }}
                                    : <label class="badge badge-soft-primary">{{ $depositRequest->account_number }}</label>
                                </h6>

                            </div>
                            <div class="d-sm-none">
                                <a class="btn btn--primary print--btn font-regular"
                                    href={{ route('admin.order.generate-invoice', [1]) }}>
                                    <i class="tio-print mr-sm-1"></i> <span>{{ translate('messages.print') }}
                                        {{ translate('messages.invoice') }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="order-invoice-right mt-3 mt-sm-0">
                            @if ($depositRequest->status == 'pending')
                                <div class="btn--container ml-auto align-items-center justify-content-end">

                                    <button class="btn btn-sm btn--danger btn-outline-danger font-regular" type="button"
                                        onclick="form_alert('deposit-request-reject-{{ $depositRequest->id }}','{{ translate('You want to reject this deposit request') }}')">
                                        <i class="tio-delete"></i> {{ translate('messages.reject') }}
                                    </button>

                                    <form
                                        action="{{ route('admin.customer.wallet.deposit-requests.reject', [$depositRequest->id]) }}"
                                        method="post" id="deposit-request-reject-{{ $depositRequest->id }}">
                                        @csrf @method('post')
                                    </form>

                                    <a class="btn btn--primary print--btn font-regular d-none d-sm-block" href="javascript:"
                                        onclick="form_alert('deposit-request-approve-{{ $depositRequest->id }}','{{ translate('You want to approve this deposit request') }}')">
                                        <i class="tio-done mr-sm-1"></i> <span>{{ translate('messages.approve') }}</span>
                                    </a>

                                    <form
                                        action="{{ route('admin.customer.wallet.deposit-requests.approve', [$depositRequest->id]) }}"
                                        method="post" id="deposit-request-approve-{{ $depositRequest->id }}">
                                        @csrf @method('post')
                                    </form>
                                </div>
                            @endif

                            <div class="text-right mt-3 order-invoice-right-contents text-capitalize">
                                <h6>
                                    {{ translate('status') }} :

                                    <span
                                        class="badge 
                                    @if ($depositRequest->status == 'rejected' || $depositRequest->status == 'canceled') badge-soft-danger
                                            @elseif ($depositRequest->status == 'approved') badge-soft-primary
                                            @else 
                                            badge-soft-warning @endif
                                    ml-2 ml-sm-3 text-capitalize">
                                        {{ translate(str_replace('_', ' ', ucwords($depositRequest->status))) }}
                                    </span>

                                </h6>
                                <h6 class="text-capitalize">
                                    {{ translate('messages.payment') }} {{ translate('messages.method') }}
                                    : <label
                                        class="badge badge-soft-primary">{{ $depositRequest->payment_channel }}</label>
                                </h6>

                                <h6 class="text-capitalize">
                                    {{ translate('messages.amount') }}
                                    : <label class="badge badge-soft-primary">{{ $depositRequest->amount }}</label>
                                </h6>

                                <h5 class="text-dark">
                                    {{ translate('messages.attachment') }}:
                                </h5>
                                <button class="btn w-100 px-0" data-toggle="modal" data-target="#imagemodal"
                                    title="{{ translate('messages.order') }} {{ translate('messages.attachment') }}">
                                    <div class="gallary-card ml-auto">
                                        <img src="{{ $depositRequest->proof_img_url }}"
                                            alt="{{ translate('messages.attachment') }}" class="initial--22 object-cover">
                                    </div>
                                </button>
                                <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel">
                                                    {{ translate('messages.attachment') }}</h4>
                                                <button type="button" class="close" data-dismiss="modal"><span
                                                        aria-hidden="true">&times;</span><span
                                                        class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="{{ $depositRequest->proof_img_url }}" class="initial--22 w-100">
                                            </div>
                                            <div class="modal-footer">
                                                {{-- <a class="btn btn-primary"
                                                    href="{{ route('admin.file-manager.download', base64_encode('public/order/' . $order->order_attachment)) }}"><i
                                                        class="tio-download"></i> {{ translate('messages.download') }}
                                                </a> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->
                </div>
            </div>

            @if ($depositRequest->owner != null)
                <div class="col-lg-4">
                    <!-- Card -->
                    <div class="card">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-header-title">
                                <span class="card-header-icon">
                                    <i class="tio-user"></i>
                                </span>
                                <span>{{ $depositRequest->owner->f_name . ' ' . $depositRequest->owner->l_name }}</span>
                            </h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <a class="customer--information-single media align-items-center"
                                href="{{ route('admin.customer.view', ['user_id' => $depositRequest->owner->id]) }}">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img"
                                        onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'"
                                        src="{{ asset('storage/app/public/profile/' . $depositRequest->owner->image) }}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <ul class="list-unstyled m-0">
                                        <li class="pb-1">
                                            <i class="tio-email mr-2"></i>
                                            {{ $depositRequest->owner->email }}
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            {{ $depositRequest->owner->phone }}
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-shopping-basket-outlined mr-2"></i>
                                            {{ $depositRequest->owner->order_count }}
                                            {{ translate('messages.orders') }}
                                        </li>
                                    </ul>
                                </div>
                            </a>

                            <hr>


                            {{-- @foreach ($customer->addresses as $address)
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{ translate('messages.addresses') }}</h5>
                                </div>
                                <ul class="list-unstyled list-unstyled-py-2">
                                    <li>
                                        <i class="tio-tab mr-2"></i>
                                        {{ $address['address_type'] }}
                                    </li>
                                    @if ($address['contact_person_umber'])
                                        <li>
                                            <i class="tio-android-phone-vs mr-2"></i>
                                            {{ $address['contact_person_number'] }}
                                        </li>
                                    @endif
                                    <li>
                                        <a target="_blank"
                                            href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $address['latitude'] }}+{{ $address['longitude'] }}"
                                            class="text--hover">
                                            <i class="tio-poi mr-2"></i>
                                            {{ $address['address'] }}
                                        </a>
                                    </li>
                                </ul>
                                <hr>
                            @endforeach --}}

                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </div>
            @endif
        </div>
        <!-- End Row -->
    </div>
@endsection

@push('script_2')
    <script></script>
@endpush
