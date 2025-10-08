@extends('layouts.admin.app')

@section('title', translate('Create Company Account'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--26" alt="">
                </span>
                <span>
                    {{ translate('messages.create') }} {{ translate('messages.company') }}
                    {{ translate('messages.account') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.business-settings.company-accounts.store') }}" method="post"
                            enctype="multipart/form-data" id="company-account-form">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label" for="nickname">{{ translate('messages.name') }}</label>
                                        <input name="nickname" id="nickname" type="text" class="form-control"
                                            placeholder="{{ translate('messages.name') }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label" for="bank-name">{{ translate('Bank') }}</label>
                                        <select name="bank_name" id="bank-name" class="form-control" required>
                                            <option disabled selected value>---{{ translate('messages.select') }}---
                                            </option>
                                            <option value="GCash" {{-- {{ $zone->id == $banner->zone_id ? 'selected' : '' }} --}}>GCash</option>
                                            <option value="Paymaya" {{-- {{ $zone->id == $banner->zone_id ? 'selected' : '' }} --}}>Paymaya</option>
                                            <option value="BDO" {{-- {{ $zone->id == $banner->zone_id ? 'selected' : '' }} --}}>BDO</option>
                                            <option value="BPI" {{-- {{ $zone->id == $banner->zone_id ? 'selected' : '' }} --}}>BPI</option>
                                            <option value="Chinabank" {{-- {{ $zone->id == $banner->zone_id ? 'selected' : '' }} --}}>Chinabank</option>
                                            <option value="Metrobank" {{-- {{ $zone->id == $banner->zone_id ? 'selected' : '' }} --}}>Metrobank</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="account-name">{{ translate('messages.account') . ' ' . translate('messages.name') }}</label>
                                        <input name="account_name" id="account-name" type="text" class="form-control"
                                            placeholder="{{ translate('messages.account') . ' ' . translate('messages.name') }}"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="account-number">{{ translate('messages.account') . ' ' . translate('messages.number') }}</label>
                                        <input name="account_number" id="account-number" type="text" class="form-control"
                                            placeholder="{{ translate('messages.account') . ' ' . translate('messages.number') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="h-100 d-flex flex-column">
                                        {{-- <label class="mt-auto mb-0 d-block text-center">
                                            {{ translate('messages.campaign') }} {{ translate('messages.image') }}
                                            <small class="text-danger">* ( {{ translate('messages.ratio') }} 900x300
                                                )</small>
                                        </label> --}}
                                        <center class="py-3 my-auto">
                                            <img class="img--vertical" id="viewer"
                                                onerror="this.src='{{ asset('public/assets/admin/img/900x400/img1.jpg') }}'"
                                                src="{{ asset('public/assets/admin/img/900x400/img1.jpg') }}"
                                                alt="qr image" />
                                        </center>
                                        <div class="custom-file">
                                            <input name="qr_img" type="file" id="qr-img" class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                for="qr-img">{{ translate('messages.choose') }}
                                                {{ translate('messages.file') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" id="reset_btn"
                                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                                        <button type="submit"
                                            class="btn btn--primary">{{ translate('messages.create') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#qr-img").change(function() {
            readURL(this);
        });
        $('#reset_btn').click(function() {
            location.reload(true);
        })
    </script>
@endpush
