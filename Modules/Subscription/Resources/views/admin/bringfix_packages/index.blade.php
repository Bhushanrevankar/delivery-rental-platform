@extends('layouts.admin.app')

@section('title', 'Bringfix Packages')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-package"></i> Bringfix Packages <span class="badge badge-soft-dark ml-2">{{ $packages->total() }}</span></h1>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn--primary" href="{{ route('admin.users.subscription.bringfix.packages.create') }}">
                    <i class="tio-add"></i> Add New Package
                </a>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Card -->
    <div class="card">
        <div class="card-header py-2 border-0">
            <div class="search--button-wrapper">
                <h5 class="card-title">Package List</h5>
            </div>
        </div>
        <div class="table-responsive datatable-custom">
            <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Trips/Month</th>
                        <th>Max Distance (km)</th>
                        <th>Trip Type</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($packages as $key => $package)
                    <tr>
                        <td>{{ $packages->firstItem() + $key }}</td>
                        <td>
                            <span class="d-block font-size-sm text-body">
                                {{ Str::limit($package->name, 25, '...') }}
                            </span>
                        </td>
                        <td>{{ $package->trips_per_month }}</td>
                        <td>{{ $package->max_distance_km }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $package->trip_type)) }}</td>
                        <td>{{ \App\CentralLogics\Helpers::format_currency($package->price) }}</td>
                        <td>
                            <label class="toggle-switch toggle-switch-sm">
                                <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{ route('admin.users.subscription.bringfix.packages.status', [$package->id, $package->status ? 0 : 1]) }}'" {{ $package->status ? 'checked' : '' }}>
                                <span class="toggle-switch-label"><span class="toggle-switch-indicator"></span></span>
                            </label>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="btn btn-sm btn--primary btn-outline-primary action-btn" href="{{ route('admin.users.subscription.bringfix.packages.edit', [$package->id]) }}" title="Edit">
                                    <i class="tio-edit"></i>
                                </a>
                                <a class="btn btn-sm btn--danger btn-outline-danger action-btn" href="javascript:" onclick="form_alert('package-{{ $package->id }}', 'Want to delete this package?')" title="Delete">
                                    <i class="tio-delete-outlined"></i>
                                </a>
                                <form action="{{ route('admin.users.subscription.bringfix.packages.destroy', [$package->id]) }}" method="post" id="package-{{ $package->id }}">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if(count($packages) === 0)
        <div class="empty--data">
            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
            <h5>No data found</h5>
        </div>
        @endif

        <!-- Footer -->
        <div class="card-footer page-area">
            {!! $packages->links() !!}
        </div>
        <!-- End Footer -->
    </div>
    <!-- End Card -->
</div>
@endsection
