@extends('layouts.admin.app')

@section('title', 'User Bringfix Subscriptions')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-user"></i> User Bringfix Subscriptions <span class="badge badge-soft-dark ml-2">{{ $subscriptions->total() }}</span></h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Card -->
    <div class="card">
        <div class="card-header py-2 border-0">
            <div class="search--button-wrapper">
                <h5 class="card-title">Subscription List</h5>
            </div>
        </div>
        <div class="table-responsive datatable-custom">
            <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Package</th>
                        <th>Trips Remaining</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($subscriptions as $key => $subscription)
                    <tr>
                        <td>{{ $subscriptions->firstItem() + $key }}</td>
                        <td>
                            @if($subscription->user)
                                <a href="#">{{ $subscription->user->f_name }} {{ $subscription->user->l_name }}</a>
                            @else
                                <span class="text-muted">User not found</span>
                            @endif
                        </td>
                        <td>{{ $subscription->bringfixPackage->name }}</td>
                        <td>{{ $subscription->trips_remaining }} / {{ $subscription->trips_total }}</td>
                        <td>{{ \Carbon\Carbon::parse($subscription->expiry_date)->format('d M Y') }}</td>
                        <td>
                            <span class="badge badge-soft-{{ $subscription->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="btn btn-sm btn--primary btn-outline-primary action-btn" href="{{ route('admin.users.subscription.bringfix.list.show', [$subscription->id]) }}" title="View">
                                    <i class="tio-visible"></i>
                                </a>
                                <a class="btn btn-sm btn--danger btn-outline-danger action-btn" href="javascript:" onclick="form_alert('subscription-{{ $subscription->id }}', 'Want to delete this subscription?')" title="Delete">
                                    <i class="tio-delete-outlined"></i>
                                </a>
                                <form action="{{ route('admin.users.subscription.bringfix.list.destroy', [$subscription->id]) }}" method="post" id="subscription-{{ $subscription->id }}">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if(count($subscriptions) === 0)
        <div class="empty--data">
            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
            <h5>No data found</h5>
        </div>
        @endif

        <!-- Footer -->
        <div class="card-footer page-area">
            {!! $subscriptions->links() !!}
        </div>
        <!-- End Footer -->
    </div>
    <!-- End Card -->
</div>
@endsection
