@extends('layouts.admin.app')

@section('title', 'Credit Transactions')

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title"><i class="tio-money"></i> Credit Transactions <span class="badge badge-soft-dark ml-2">{{ $transactions->total() }}</span></h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Card -->
    <div class="card">
        <div class="card-header py-2 border-0">
            <div class="search--button-wrapper">
                <h5 class="card-title">Transaction List</h5>
            </div>
        </div>
        <div class="table-responsive datatable-custom">
            <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Transaction Type</th>
                        <th>Reference</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($transactions as $key => $transaction)
                    <tr>
                        <td>{{ $transactions->firstItem() + $key }}</td>
                        <td>
                            @if($transaction->user)
                                <a href="#">{{ $transaction->user->f_name }} {{ $transaction->user->l_name }}</a>
                            @else
                                <span class="text-muted">User not found</span>
                            @endif
                        </td>
                        <td>
                            <span class="font-weight-bold {{ $transaction->transaction_type == 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->amount }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-soft-{{ $transaction->transaction_type == 'credit' ? 'success' : 'danger' }}">
                                {{ ucfirst($transaction->transaction_type) }}
                            </span>
                        </td>
                        <td>{{ $transaction->reference }}</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y, h:i A') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if(count($transactions) === 0)
        <div class="empty--data">
            <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
            <h5>No data found</h5>
        </div>
        @endif

        <!-- Footer -->
        <div class="card-footer page-area">
            {!! $transactions->links() !!}
        </div>
        <!-- End Footer -->
    </div>
    <!-- End Card -->
</div>
@endsection
