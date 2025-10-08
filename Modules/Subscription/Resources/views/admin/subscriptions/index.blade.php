@extends('layouts.admin.app')

@section('title', 'Standard User Subscriptions')

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-user-big"></i> Standard Subscriptions <span
                            class="badge badge-soft-dark ml-2">{{ $subscriptions->total() }}</span></h1>
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
                <table id="datatable"
                    class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Package</th>
                            <th>Credits Remaining</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($subscriptions as $key => $subscription)
                            <tr>
                                <td>{{ $subscriptions->firstItem() + $key }}</td>
                                <td>
                                    @if ($subscription->subscriber)
                                        <a href="#">{{ $subscription->subscriber->f_name }}
                                            {{ $subscription->subscriber->l_name }}</a>
                                    @else
                                        <span class="text-muted">User not found</span>
                                    @endif
                                </td>
                                <td>{{ $subscription->package?->package_name ?? '--' }}</td>
                                <td>{{ $subscription->remaining_credits }} / {{ $subscription->total_credits }}</td>
                                <td>{{ \Carbon\Carbon::parse($subscription->expiry_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-soft-{{ $subscription->status ? 'success' : 'danger' }}">
                                        {{ $subscription->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert"
                                            href="javascript:" data-id="subscription-{{ $subscription->id }}"
                                            data-message="Want to delete this subscription?" title="Cancel Subscription">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                        <form
                                            action="{{ route('admin.users.subscription.list.destroy', [$subscription->id]) }}"
                                            method="post" id="subscription-{{ $subscription->id }}">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (count($subscriptions) === 0)
                <div class="empty--data">
                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
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
