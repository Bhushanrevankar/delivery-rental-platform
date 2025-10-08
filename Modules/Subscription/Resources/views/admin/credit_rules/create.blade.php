@extends('layouts.admin.app')

@section('title', 'Add New Credit Deduction Rule')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Add New Rule</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{ route('admin.users.subscription.credit-rules.store') }}" method="post">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Rule Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="E.g., Driver Medium Distance" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">User Type</label>
                                    <select name="user_type" class="form-control" required>
                                        <option value="customer">Customer</option>
                                        <option value="driver">Driver</option>
                                        <option value="merchant">Merchant</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Condition Type</label>
                                    <select name="condition_type" id="condition_type" class="form-control" required>
                                        <option value="ride_hailing">Ride Hailing (Fixed per Ride)</option>
                                        <option value="ride_share">Ride Share</option>
                                        <option value="distance_range">Distance Range</option>
                                        <option value="price_range">Price Range</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Credits to Deduct</label>
                                    <input type="number" step="0.01" name="credits_to_deduct" class="form-control" placeholder="E.g., 1.5" required>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="range_fields" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Min Value (for ranges)</label>
                                    <input type="number" step="0.01" name="min_value" class="form-control" placeholder="E.g., 5">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Max Value (for ranges)</label>
                                    <input type="number" step="0.01" name="max_value" class="form-control" placeholder="E.g., 10">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Module (Optional)</label>
                            <div class="row module-selection-cards">
                                @foreach($modules as $module)
                                <div class="col-md-3">
                                    <label class="module-card">
                                        <input class="form-check-input" type="radio" name="module_id" value="{{ $module->id }}" id="module_{{ $module->id }}">
                                        <div class="card-body">
                                            <img src="{{ asset('storage/app/public/module/' . $module->icon) }}">
                                            <span>{{ $module->module_name }}</span>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="input-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<style>
    .module-selection-cards .module-card {
        cursor: pointer;
        border: 1px solid #e7eaf3;
        border-radius: .25rem;
        text-align: center;
        transition: all .2s;
        display: block;
    }
    .module-selection-cards .module-card input {
        display: none;
    }
    .module-selection-cards .module-card img {
        width: 50px;
        margin-bottom: 10px;
    }
    .module-selection-cards .module-card span {
        display: block;
    }
    .module-selection-cards .module-card:hover {
        border-color: #007bff;
    }
    .module-selection-cards .module-card input:checked + .card-body {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
</style>
<script>
    $('#condition_type').on('change', function() {
        if (this.value === 'distance_range' || this.value === 'price_range') {
            $('#range_fields').show();
        } else {
            $('#range_fields').hide();
        }
    });

    $('.module-card input[type="radio"]').on('click', function() {
        if ($(this).hasClass('selected')) {
            $(this).prop('checked', false);
            $(this).removeClass('selected');
        } else {
            $('.module-card input[type="radio"]').removeClass('selected');
            $(this).addClass('selected');
        }
    });
</script>
@endpush
