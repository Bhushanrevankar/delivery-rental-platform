@extends('layouts.admin.app')

@section('title',translate('Add new zone'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/zone.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.Add new zone')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row g-3">
            <div class="col-12">
                <form action="{{route('admin.zone.store')}}" method="post" id="zone_form" class="shadow--card">
                    @csrf
                    <div class="row justify-content-between">
                        <div class="col-lg-12 mb-2">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title m-0 d-flex align-items-center">
                                        <span class="card-header-icon mr-2"><i class="tio-user"></i></span>
                                        <span>{{translate('messages.zone_info')}}</span>
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="zone-setup-instructions">
                                                <div class="zone-setup-top">
                                                    <h6 class="subtitle">{{ translate('Instructions') }}</h6>
                                                    <p>
                                                        {{ translate('Create zone by click on map and connect the dots together') }}
                                                    </p>
                                                </div>
                                                <div class="zone-setup-item">
                                                    <div class="zone-setup-icon">
                                                        <i class="tio-hand-draw"></i>
                                                    </div>
                                                    <div class="info">
                                                        {{ translate('Use this to drag map to find proper area') }}
                                                    </div>
                                                </div>
                                                <div class="zone-setup-item">
                                                    <div class="zone-setup-icon">
                                                        <i class="tio-free-transform"></i>
                                                    </div>
                                                    <div class="info">
                                                        {{ translate('Click this icon to start pin points in the map and connect them to draw a zone . Minimum 3  points required') }}
                                                    </div>
                                                </div>
                                                <div class="instructions-image mt-4">
                                                    <img src="{{asset('public/assets/admin/img/instructions.gif')}}" alt="instructions">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-7 zone-setup">
                                            <div class="pl-xl-5 pl-xxl-0">
                                                <div class="form-group mb-3">
                                                    <label class="input-label"
                                                        for="exampleFormControlInput1">{{translate('messages.name')}}</label>
                                                    <input type="text" name="name" id="name" class="form-control" placeholder="{{translate('messages.new_zone')}}" value="{{old('name')}}" required>
                                                </div>
                                                <div class="form-group mb-3 d-none">
                                                    <label class="input-label"
                                                        for="exampleFormControlInput1">{{ translate('Coordinates') }}<span class="input-label-secondary" title="{{translate('messages.draw_your_zone_on_the_map')}}">{{translate('messages.draw_your_zone_on_the_map')}}</span></label>
                                                        <textarea type="text" rows="8" name="coordinates"  id="coordinates" class="form-control" readonly></textarea>
                                                </div>
                                                <div class="map-warper rounded mt-0">
                                                    <input id="pac-input" class="controls rounded" title="{{translate('messages.search_your_location_here')}}" type="text" placeholder="{{translate('messages.search_here')}}"/>
                                                    <div id="map-canvas" class="rounded"></div>
                                                </div>
                                            </div>
                                        </div>      
                                        <div class="col-md-3">
                                            <div class="form-group mt-3">
                                                <label class="input-label" for="opening_time">{{translate('messages.opening_time')}}</label>
                                                <input name="opening_time" type="time" id="opening_time" class="form-control" required>
                                            </div>    
                                        </div>                                  
                                        <div class="col-md-3">
                                            <div class="form-group mt-3">
                                                <label class="input-label" for="closing_time">{{translate('messages.closing_time')}}</label>
                                                <input name="closing_time" type="time" id="closing_time" class="form-control" required>
                                            </div>    
                                        </div>                                  
                                        <div class="col-md-6">
                                            <div class="form-group mt-3">
                                                <label class="input-label" for="available_module">{{translate('messages.available_module')}}</label>
                                                <select name="available_module[]" id="available_module" class="form-control js-select2-custom" multiple required>
                                                    @foreach (\App\Models\Module::active()->get() as $module)
                                                        <option value="{{$module->id}}">{{$module->module_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>                                  
                                    </div>
                                </div>                                
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title m-0 d-flex align-items-center">
                                        <span class="card-header-icon mr-2"><i class="tio-user"></i></span>
                                        <span>{{translate('messages.zonal_manager_info')}}</span>
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="f_name">{{translate('messages.first')}} {{translate('messages.name')}}</label>
                                                <input type="text" name="f_name" class="form-control" placeholder="{{translate('messages.first')}} {{translate('messages.name')}}"
                                                        value="{{old('f_name')}}"  required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="l_name">{{translate('messages.last')}} {{translate('messages.name')}}</label>
                                                <input type="text" name="l_name" class="form-control" placeholder="{{translate('messages.last')}} {{translate('messages.name')}}"
                                                value="{{old('l_name')}}"  required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="phone">{{translate('messages.phone')}}</label>
                                                <input type="tel" name="phone" class="form-control" placeholder="{{ translate('messages.Ex:') }} 017********"
                                                value="{{old('phone')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group mb-0">
                                                <label class="input-label" for="email">{{translate('messages.email')}}</label>
                                                <input type="email" name="email" class="form-control" placeholder="{{ translate('messages.Ex:') }} ex@example.com"
                                                value="{{old('email')}}"  required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="js-form-message form-group mb-0">
                                                <label class="input-label" for="signupSrPassword">{{translate('messages.password')}}</label>
        
                                                <div class="input-group input-group-merge">
                                                    <input type="password" class="js-toggle-password form-control" name="password" id="signupSrPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" required
                                                    data-msg="Your password is invalid. Please try again."
                                                    data-hs-toggle-password-options='{
                                                    "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                                    "defaultClass": "tio-hidden-outlined",
                                                    "showClass": "tio-visible-outlined",
                                                    "classChangeTarget": ".js-toggle-passowrd-show-icon-1"
                                                    }'>
                                                    <div class="js-toggle-password-target-1 input-group-append">
                                                        <a class="input-group-text" href="javascript:;">
                                                            <i class="js-toggle-passowrd-show-icon-1 tio-visible-outlined"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="js-form-message form-group mb-0">
                                                <label class="input-label" for="signupSrConfirmPassword">{{translate('messages.confirm_password')}}</label>
                                                <div class="input-group input-group-merge">
                                                <input type="password" class="js-toggle-password form-control" name="confirmPassword" id="signupSrConfirmPassword" placeholder="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" aria-label="{{translate('messages.password_length_placeholder',['length'=>'5+'])}}" required
                                                        data-msg="Password does not match the confirm password."
                                                        data-hs-toggle-password-options='{
                                                        "target": [".js-toggle-password-target-1", ".js-toggle-password-target-2"],
                                                        "defaultClass": "tio-hidden-outlined",
                                                        "showClass": "tio-visible-outlined",
                                                        "classChangeTarget": ".js-toggle-passowrd-show-icon-2"
                                                        }'>
                                                    <div class="js-toggle-password-target-2 input-group-append">
                                                        <a class="input-group-text" href="javascript:;">
                                                        <i class="js-toggle-passowrd-show-icon-2 tio-visible-outlined"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn--container mt-3 justify-content-end">
                        <button id="reset_btn" type="reset" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
    auto_grow();
    function auto_grow() {
        let element = document.getElementById("coordinates");
        element.style.height = "5px";
        element.style.height = (element.scrollHeight)+"px";
    }

    </script>
    <script>
        $(document).on('ready', function () {
            $('.js-toggle-password').each(function () {
                new HSTogglePassword(this).init()
            });
            // INITIALIZATION OF FORM VALIDATION
            // =======================================================
            $('.js-validate').each(function() {
                $.HSCore.components.HSValidation.init($(this), {
                    rules: {
                    confirmPassword: {
                        equalTo: '#signupSrPassword'
                    }
                    }
                });
            });
            
            $("#zone_form").on('keydown', function(e){
                if (e.keyCode === 13) {
                    e.preventDefault();
                }
            })
        });
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&callback=initialize&libraries=drawing,places&v=3.49"></script>

    <script>
        var map; // Global declaration of the map
        var drawingManager;
        var lastpolygon = null;
        var polygons = [];

        function resetMap(controlDiv) {
            // Set CSS for the control border.
            const controlUI = document.createElement("div");
            controlUI.style.backgroundColor = "#fff";
            controlUI.style.border = "2px solid #fff";
            controlUI.style.borderRadius = "3px";
            controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
            controlUI.style.cursor = "pointer";
            controlUI.style.marginTop = "8px";
            controlUI.style.marginBottom = "22px";
            controlUI.style.textAlign = "center";
            controlUI.title = "Reset map";
            controlDiv.appendChild(controlUI);
            // Set CSS for the control interior.
            const controlText = document.createElement("div");
            controlText.style.color = "rgb(25,25,25)";
            controlText.style.fontFamily = "Roboto,Arial,sans-serif";
            controlText.style.fontSize = "10px";
            controlText.style.lineHeight = "16px";
            controlText.style.paddingLeft = "2px";
            controlText.style.paddingRight = "2px";
            controlText.innerHTML = "X";
            controlUI.appendChild(controlText);
            // Setup the click event listeners: simply set the map to Chicago.
            controlUI.addEventListener("click", () => {
                lastpolygon.setMap(null);
                $('#coordinates').val('');

            });
        }

        function initialize() {
            @php($default_location=\App\Models\BusinessSetting::where('key','default_location')->first())
            @php($default_location=$default_location->value?json_decode($default_location->value, true):0)
            var myLatlng = { lat: {{$default_location?$default_location['lat']:'23.757989'}}, lng: {{$default_location?$default_location['lng']:'90.360587'}} };


            var myOptions = {
                zoom: 13,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                editable: true
                }
            });
            drawingManager.setMap(map);


            //get current location block
            // infoWindow = new google.maps.InfoWindow();
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                    };
                    map.setCenter(pos);
                });
            }

            drawingManager.addListener("overlaycomplete", function(event) {
                if(lastpolygon)
                {
                    lastpolygon.setMap(null);
                }
                $('#coordinates').val(event.overlay.getPath().getArray());
                lastpolygon = event.overlay;
                auto_grow();
            });

            const resetDiv = document.createElement("div");
            resetMap(resetDiv, lastpolygon);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(resetDiv);

            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });
            let markers = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }
                const icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25),
                };
                // Create a marker for each place.
                markers.push(
                    new google.maps.Marker({
                    map,
                    icon,
                    title: place.name,
                    position: place.geometry.location,
                    })
                );

                if (place.geometry.viewport) {
                    // Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
                });
                map.fitBounds(bounds);
            });
        }
        // initialize();
        function set_all_zones()
        {
            $.get({
                url: '{{route('admin.zone.zoneCoordinates')}}',
                dataType: 'json',
                success: function (data) {
                    for(var i=0; i<data.length;i++)
                    {
                        polygons.push(new google.maps.Polygon({
                            paths: data[i],
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: "#FF0000",
                            fillOpacity: 0.1,
                        }));
                        polygons[i].setMap(map);
                    }

                },
            });
        }
        $(document).on('ready', function(){
            set_all_zones();
        });

    </script>
    <script>
        $('#reset_btn').click(function(){
            $('#name').val(null);
            $('#minimum_delivery_charge').val(null);
            $('#delivery_charge_per_km').val(null);

            lastpolygon.setMap(null);
            $('#coordinates').val(null);
        })
    </script>
    <script>
        $('#zone_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.zone.store')}}',
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
                        toastr.success("{{translate('messages.zone_added_successfully')}}", {
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
