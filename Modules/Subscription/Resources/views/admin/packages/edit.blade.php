@extends('layouts.admin.app')

@section('title', 'Edit Subscription Package')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Edit Package</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <form action="{{ route('admin.users.subscription.packages.update', $package->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">Package Name</label>
                                    <input type="text" name="package_name" class="form-control" value="{{ $package->package_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="input-label">User Type</label>
                                    <select name="user_type" class="form-control" required>
                                        <option value="customer" {{ $package->user_type == 'customer' ? 'selected' : '' }}>Customer</option>
                                        <option value="driver" {{ $package->user_type == 'driver' ? 'selected' : '' }}>Driver</option>
                                        <option value="merchant" {{ $package->user_type == 'merchant' ? 'selected' : '' }}>Merchant</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">Price ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ $package->price }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">Validity (Days)</label>
                                    <input type="number" name="validity" class="form-control" value="{{ $package->validity }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">Credits</label>
                                    <input type="number" name="credits" class="form-control" value="{{ $package->credits }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">Members Included</label>
                                    <input type="number" name="members_included" class="form-control" value="{{ $package->members_included }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="input-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ $package->status ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !$package->status ? 'selected' : '' }}>Inactive</option>
                            </select>
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
