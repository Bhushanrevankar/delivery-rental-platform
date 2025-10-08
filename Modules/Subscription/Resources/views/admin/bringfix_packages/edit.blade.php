@extends('layouts.admin.app')

@section('title', 'Edit Bringfix Package')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Edit Bringfix Package</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{ route('admin.users.subscription.bringfix-packages.update', $bringfixPackage->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Package Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $bringfixPackage->name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Trips Per Month</label>
                                    <input type="number" name="trips_per_month" class="form-control" value="{{ $bringfixPackage->trips_per_month }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Max Distance (km)</label>
                                    <input type="number" step="0.01" name="max_distance_km" class="form-control" value="{{ $bringfixPackage->max_distance_km }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Price ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $bringfixPackage->price }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Trip Type</label>
                                    <select name="trip_type" class="form-control" required>
                                        <option value="one_way" {{ $bringfixPackage->trip_type == 'one_way' ? 'selected' : '' }}>One Way</option>
                                        <option value="two_way" {{ $bringfixPackage->trip_type == 'two_way' ? 'selected' : '' }}>Two Way</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ $bringfixPackage->status ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ !$bringfixPackage->status ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
