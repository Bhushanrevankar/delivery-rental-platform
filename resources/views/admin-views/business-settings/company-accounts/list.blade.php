@extends('layouts.admin.app')

@section('title', translate('messages.company') . ' ' . translate('messages.accounts'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->

        <div class="page-header">
            <h1 class="page-header-title justify-content-between">
                <div>
                    <span class="page-header-icon">
                        <img src="{{ asset('/public/assets/admin/img/payment.png') }}" class="w--22" alt="">
                    </span>
                    <span>
                        {{ translate('messages.company') }} {{ translate('messages.accounts') }}
                        {{ translate('messages.setup') }}
                    </span>
                </div>
                <a href="{{ route('admin.business-settings.company-accounts.create') }}"
                    class="btn btn-sm btn-primary d-flex align-items-center">
                    <i class="tio-add"></i>
                    <span>{{ translate('messages.add') . ' ' . translate('messages.account') }}</span>
                </a>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            @foreach ($companyAccounts as $companyAccount)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body pt-3">
                            <div class="row">
                                <div class="col-10">
                                    <h5 class="card-title">
                                        <span>{{ $companyAccount->nickname }}</span>
                                    </h5>
                                </div>
                                <div class="col">
                                    <label class="toggle-switch toggle-switch-sm"
                                        for="company-account-status-{{ $companyAccount->id }}">
                                        <input type="checkbox"
                                            onclick="status_form_alert('company-account-status-form-{{ $companyAccount->id }}','{{ translate('Want to change status for this company account?') }}', event)"
                                            class="toggle-switch-input"
                                            id="company-account-status-{{ $companyAccount->id }}"
                                            {{ $companyAccount->availability ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                    <form
                                        action="{{ route('admin.business-settings.company-accounts.status', [$companyAccount->id]) }}"
                                        method="post" id="company-account-status-form-{{ $companyAccount->id }}">
                                        @csrf @method('post')
                                    </form>
                                </div>
                            </div>
                            <p class="card-text">{{ $companyAccount->bank_name }}</p>
                            <p class="card-text">
                                Account Name: {{ $companyAccount->account_name }}
                                <br>
                                Account Number: {{ $companyAccount->account_number }}
                            </p>
                            <div class="btn--container justify-content-end">
                                @if ($companyAccount->qr_img_url)
                                    <button class="btn action-btn btn-outline-warning" data-toggle="modal"
                                        data-target="#imagemodal-{{ $companyAccount->id }}"
                                        title="{{ translate('QR Code') }}">
                                        <i class="tio-qr-code"></i>
                                    </button>

                                    <div class="modal fade" id="imagemodal-{{ $companyAccount->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="myModalLabel-{{ $companyAccount->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel-{{ $companyAccount->id }}">
                                                        {{ translate('QR Code') }}</h4>
                                                    <button type="button" class="close" data-dismiss="modal"><span
                                                            aria-hidden="true">&times;</span><span
                                                            class="sr-only">{{ translate('messages.cancel') }}</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <img src="{{ $companyAccount->qr_img_url }}" class="initial--22 w-100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <a class="btn action-btn btn-outline-primary"
                                    href="{{ route('admin.business-settings.company-accounts.edit', [$companyAccount->id]) }}"
                                    title="{{ translate('messages.edit') . ' ' . translate('messages.company') . ' ' . translate('messages.account') }}">
                                    <i class="tio-edit"></i>
                                </a>
                                <a class="btn action-btn btn-outline-danger" href="javascript:"
                                    onclick="form_alert('company-account-delete-{{ $companyAccount->id }}','{{ translate('You want to remove this company account') }}')"
                                    title="{{ translate('messages.delete') }} {{ translate('messages.company') . ' ' . translate('messages.account') }}">
                                    <i class="tio-delete-outlined"></i>
                                </a>
                                <form
                                    action="{{ route('admin.business-settings.company-accounts.delete', [$companyAccount->id]) }}"
                                    method="post" id="company-account-delete-{{ $companyAccount->id }}">
                                    @csrf @method('delete')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if (count($companyAccounts) === 0)
                <div class="col empty--data">
                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
                    </h5>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function status_form_alert(id, message, e) {
            e.preventDefault();
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.Yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + id).submit()
                }
            })
        }
    </script>
@endpush
