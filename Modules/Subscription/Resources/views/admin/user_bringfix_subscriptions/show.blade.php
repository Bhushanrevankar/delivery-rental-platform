@extends('layouts.admin.app')

@section('title', 'View User Bringfix Subscription')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">Subscription Details</h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Subscription #{{ $userBringfixSubscription->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>User:</strong> {{ $userBringfixSubscription->user->f_name }} {{ $userBringfixSubscription->user->l_name }}</p>
                            <p><strong>Package:</strong> {{ $userBringfixSubscription->bringfixPackage->name }}</p>
                            <p><strong>Trips:</strong> {{ $userBringfixSubscription->trips_remaining }} / {{ $userBringfixSubscription->trips_total }} remaining</p>
                            <p><strong>Expiry Date:</strong> {{ \Carbon\Carbon::parse($userBringfixSubscription->expiry_date)->format('d M Y, h:i A') }}</p>
                            <p><strong>Status:</strong> <span class="badge badge-soft-{{ $userBringfixSubscription->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($userBringfixSubscription->status) }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Pickup Address:</strong> {{ $userBringfixSubscription->pickup_address }}</p>
                            <p><strong>Dropoff Address:</strong> {{ $userBringfixSubscription->dropoff_address }}</p>
                            <p><strong>Route Distance:</strong> {{ $userBringfixSubscription->route_distance_km }} km</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Trip Schedule</h5>
                </div>
                <div class="card-body">
                    @if($userBringfixSubscription->schedules->count() > 0)
                        <ul class="list-group">
                            @foreach($userBringfixSubscription->schedules as $schedule)
                                <li class="list-group-item">
                                    <strong>{{ \Carbon\Carbon::now()->startOfWeek()->addDays($schedule->day_of_week)->format('l') }}:</strong>
                                    {{ \Carbon\Carbon::parse($schedule->time_slot)->format('h:i A') }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No schedule set.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
