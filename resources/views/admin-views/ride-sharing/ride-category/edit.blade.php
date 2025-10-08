@extends('layouts.admin.app')

@section('title',translate('messages.edit_vehicle_category'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.edit_vehicle_category')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.ride.vehicle-category.update', ['vehicle_category'=>$vehicle_category->id])}}" method="post">
                    @csrf
                    @method('put')
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{translate('messages.ex_:_scooty')}}" maxlength="191" required value="{{$vehicle_category->name}}">
                            </div>                                    
                        </div>
                        <div class="col-6">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.vehicle_type')}}</label>
                            <select name="dm_vehicle_id" class="form-control js-select2-custom" required>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{$vehicle->id}}" {{$vehicle_category->dm_vehicle_id == $vehicle->id ? 'selected' : ''}}>{{$vehicle->type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.base_fare')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                                <input type="number" step=".01" name="base_fare" class="form-control" placeholder="10" min="0" max="9999999999.99" required value="{{$vehicle_category->base_fare}}">
                            </div>                                    
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.per_km_fare')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                                <input type="number" step=".01" name="per_km_fare" class="form-control" placeholder="4" min="0" max="9999999999.99" required value="{{$vehicle_category->per_km_fare}}">
                            </div>      
                        </div>
                        <div class="col-sm-4 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.per_min_waiting_fare')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                                <input type="number" step=".01" name="per_min_waiting_fare" class="form-control" placeholder="2" min="0" max="9999999999.99" required value="{{$vehicle_category->per_min_waiting_fare}}">
                            </div>      
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')

@endpush
