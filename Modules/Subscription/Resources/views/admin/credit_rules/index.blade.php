@extends('layouts.admin.app')

@section('title', 'Credit Deduction Rules')

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-settings"></i> Credit Deduction Rules <span
                            class="badge badge-soft-dark ml-2">{{ $rules->total() }}</span></h1>
                </div>
                <div class="col-sm-auto">
                    <a class="btn btn--primary" href="{{ route('admin.users.subscription.credit-rules.create') }}">
                        <i class="tio-add"></i> Add New Rule
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">Rule List</h5>
                </div>
            </div>
            <div class="table-responsive datatable-custom">
                <table id="datatable"
                    class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Rule Name</th>
                            <th>User Type</th>
                            <th>Module</th>
                            <th>Condition Type</th>
                            <th>Credits</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rules as $key => $rule)
                            <tr>
                                <td>{{ $rules->firstItem() + $key }}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{ Str::limit($rule->name, 25, '...') }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($rule->user_type) }}</td>
                                <td>{{ $rule->module->module_name ?? 'All' }}</td>
                                <td>
                                    {{ ucfirst(str_replace('_', ' ', $rule->condition_type)) }}
                                    @if ($rule->min_value !== null || $rule->max_value !== null)
                                        ({{ $rule->min_value }} - {{ $rule->max_value }})
                                    @endif
                                </td>
                                <td>{{ $rule->credits_to_deduct }}</td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input"
                                            onclick="location.href='{{ route('admin.users.subscription.credit-rules.status', [$rule->id, $rule->status ? 0 : 1]) }}'"
                                            {{ $rule->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label"><span
                                                class="toggle-switch-indicator"></span></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                                            href="{{ route('admin.users.subscription.credit-rules.edit', [$rule->id]) }}"
                                            title="Edit">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn--danger btn-outline-danger action-btn form-alert"
                                            href="javascript:" data-id="rule-{{ $rule->id }}"
                                            data-message="Want to delete this rule?" title="Delete">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                        <form
                                            action="{{ route('admin.users.subscription.credit-rules.destroy', [$rule->id]) }}"
                                            method="post" id="rule-{{ $rule->id }}">
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (count($rules) === 0)
                <div class="empty--data">
                    <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>No data found</h5>
                </div>
            @endif

            <!-- Footer -->
            <div class="card-footer page-area">
                {!! $rules->links() !!}
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection
