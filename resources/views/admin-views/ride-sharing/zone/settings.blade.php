@extends('layouts.admin.app')

@section('title',translate('messages.zone_settings'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    {{-- <div class="page-header pb-0">
        <h1 class="page-header-title text-break">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/zone.png')}}" class="w--26" alt="">
            </span>
            <span>{{$zone->name}}</span>
        </h1>
    </div> --}}
    <!-- End Page Header -->
    <!-- Page Heading -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="zone">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <img class="w--22" src="{{asset('public/assets/admin/img/zone.png')}}" alt="">
                        </span>
                        <span>{{translate('messages.zone_settings')}}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.zone.update-settings',['id'=>$zone['id']])}}" id="zone-setup" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-lg-4">
                                <div class="form-group mb-sm-0 mb-2">
                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="live_tracking">
                                    <span class="pr-2">{{translate('messages.live_tracking')}}</span>
                                        <input type="checkbox" value="1" class="toggle-switch-input" name="live_tracking" id="live_tracking" {{$zone->live_tracking?'checked':''}}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="form-group mb-0">
                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="rainy_weather_alert">
                                    <span class="pr-2 text-capitalize">{{translate('messages.rainy_weather_alert')}}</span>
                                        <input type="checkbox" value="1" class="toggle-switch-input" id="rainy_weather_alert" {{$zone->rainy_weather_alert?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-4 mt-1">
                                <label class="input-label text-capitalize">{{translate('messages.opening_time')}}</label>
                                <input type="time" name="opening_time" class="form-control" value="{{date('H:i', strtotime($zone->opening_time))??''}}">
                            </div>
                            <div class="form-group col-sm-4 mt-1">
                                <label class="input-label text-capitalize">{{translate('messages.closing_time')}}</label>
                                <input type="time" name="closing_time" class="form-control" value="{{date('H:i', strtotime($zone->closing_time))??''}}">
                            </div>
                            <div class="form-group col-sm-4 mt-1">
                                <label class="input-label text-capitalize">{{translate('messages.delivery_price_increase_on_rainy_weather')}}(%)</label>
                                <input type="number" name="delivery_price_increase" step="0.01" min="0" max="100" class="form-control" placeholder="00" value="{{$zone->delivery_price_increase??'0'}}">
                            </div>
                            <div class="card col-12">
                                <div class="card-header">{{translate('module_wise_settings')}}</div>
                                <div class="card-body">
                                    @foreach ($zone->modules as $module)
                                        <div class="border-bottom mb-3">{{translate('messages.module')}}: {{$module->module_name}}</div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group mb-sm-0 mb-2">
                                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="pod-{{$module->id}}">
                                                    <span class="pr-2">{{translate('messages.pod')}}</span>
                                                        <input type="hidden" value="0" name="module[{{$module->id}}][pod]">
                                                        <input type="checkbox" value="1" class="toggle-switch-input" name="module[{{$module->id}}][pod]" id="pod-{{$module->id}}" {{$module->pivot->pod?'checked':''}}>
                                                        <span class="toggle-switch-label text">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group mb-0">
                                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="cod-{{$module->id}}">
                                                    <span class="pr-2">{{translate('messages.cod')}}</span>
                                                        <input type="hidden" value="0" name="module[{{$module->id}}][cod]">
                                                        <input type="checkbox" value="1" class="toggle-switch-input" name="module[{{$module->id}}][cod]" id="cod-{{$module->id}}" {{$module->pivot->cod?'checked':''}}>
                                                        <span class="toggle-switch-label text">
                                                            <span class="toggle-switch-indicator"></span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="input-label text-capitalize">{{translate('messages.minimum_shipping_charge')}}({{\App\CentralLogics\Helpers::currency_code()}})</label>
                                                    <input type="number" name="module[{{$module->id}}][minimum_shipping_charge]" step="0.01" min="0" max="9999999999" class="form-control" placeholder="10" value="{{$module->pivot->minimum_shipping_charge??'0'}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="input-label text-capitalize">{{translate('messages.per_km_shipping_charge')}}({{\App\CentralLogics\Helpers::currency_code()}})</label>
                                                    <input type="number" name="module[{{$module->id}}][per_km_shipping_charge]" step="0.01" min="0" max="9999999999" class="form-control" placeholder="5" value="{{$module->pivot->per_km_shipping_charge??'0'}}">
                                                </div>
                                            </div>
                                        </div>                                            
                                    @endforeach                                        
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="justify-content-end btn--container">
                                    <button type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                    <button type="submit" class="btn btn--primary">{{translate('save_changes')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        {{-- <span class="card-header-icon"><i class="tio-clock"></i></span> --}}
                        <span>{{translate('messages.incentive')}}</span>
                    </h5>
                </div>
                <div class="card-body" id="schedule">
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <table id="columnSearchDatatable"
                                class="table table-bordered table-thead-bordered table-nowrap table-align-middle card-table"
                                data-hs-datatables-options='{
                                "order": [],
                                "orderCellsTop": true,
                                "paging":false
                                }'>
                                <thead>
                                    <tr>
                                        <th>{{translate('messages.order_count')}}</th>
                                        <th>{{translate('messages.incentive')}}</th>
                                        <th>{{translate('messages.action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($zone->incentives as $incentive)
                                        <tr>
                                            <td>{{$incentive->order_count}}</td>
                                            <td>{{\App\CentralLogics\Helpers::format_currency($incentive->incentive)}}</td>
                                            <td>
                                                <div class="btn--container justify-content-center">
                                                    <a class="btn action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('attribute-{{$incentive->id}}','{{translate('messages.want_to_delece_this_incentive')}}')" title="{{translate('messages.delete')}}"><i class="tio-delete-outlined"></i></a>
                                                    <form action="{{route('admin.zone.incentive.destory',['id'=>$incentive->id])}}" method="post" id="attribute-{{$incentive->id}}">
                                                        @csrf @method('delete')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>                                        
                                    @endforeach

                                    <tr>
                                        <td colspan="3">
                                            <form action="{{route('admin.zone.incentive.store',['zone_id'=>$zone->id])}}" method="POST">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="number" name="order_count" id="" class="form-control" placeholder="{{translate('messages.enter_order_count')}}" required>
                                                    <input type="number" name="incentive" id="" class="form-control" step=".01" placeholder="{{translate('messages.enter_incentive')}}" required>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="submit" title="{{translate('messages.add_incentive')}}"><i class="tio-add"></i></button>
                                                    </div>
                                                </div>                                            
                                            </form>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>                            
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create schedule modal -->

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{translate('messages.Create Schedule')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:" method="post" id="add-schedule">
                    @csrf
                    <input type="hidden" name="day" id="day_id_input">
                    <input type="hidden" name="store_id" value="{{$zone->id}}">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">{{translate('messages.Start time')}}:</label>
                        <input type="time" class="form-control" name="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">{{translate('messages.End time')}}:</label>
                        <input type="time" class="form-control" name="end_time" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{{translate('messages.Submit')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<script>
    $('#zone-setup').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: "{{route('admin.zone.update-settings',['id'=>$zone->id])}}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#loading').hide();
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    toastr.success("{{translate('messages.zone_updated_successfully')}}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    setTimeout(function () {
                        location.href = "{{route('admin.zone.home')}}";
                    }, 2000);
                }
            }
        });
    });
</script>
@endpush
