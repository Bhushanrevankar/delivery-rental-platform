<?php

namespace App\Http\Controllers\Admin\Ride;

use App\Http\Controllers\Controller;
use App\Models\DMVehicle;
use App\Models\RideCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class RideCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $key = $request->query('key');
        $vehicle_categories = RideCategory::
        when($key, function($query)use($key){
            $key = explode(' ', $key);
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->paginate(config('default_pagination'));
        $vehicles = DMVehicle::all();
        return view('admin-views.ride-sharing.ride-category.index', compact('vehicle_categories', 'vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required'
        ]);

        $vehicle_category = new RideCategory();
        $vehicle_category->name = $request->name;
        $vehicle_category->dm_vehicle_id = $request->dm_vehicle_id;
        $vehicle_category->base_fare = $request->base_fare;
        $vehicle_category->per_km_fare = $request->per_km_fare;
        $vehicle_category->per_min_waiting_fare = $request->per_min_waiting_fare;
        $vehicle_category->save();

        Toastr::success(translate('messages.vehicle_category_added_successfully'));
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RideCategory  $vehicle_category
     * @return \Illuminate\Http\Response
     */
    public function show(RideCategory $vehicle_category)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RideCategory  $vehicle_category
     * @return \Illuminate\Http\Response
     */
    public function edit(RideCategory $vehicle_category)
    {
        $vehicles = DMVehicle::all();
        return view('admin-views.ride-sharing.ride-category.edit', compact('vehicle_category', 'vehicles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RideCategory  $vehicle_category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RideCategory $vehicle_category)
    {
        $request->validate([
            'name'=>'required'
        ]);
        $vehicle_category->name = $request->name;
        $vehicle_category->dm_vehicle_id = $request->dm_vehicle_id;
        $vehicle_category->base_fare = $request->base_fare;
        $vehicle_category->per_km_fare = $request->per_km_fare;
        $vehicle_category->per_min_waiting_fare = $request->per_min_waiting_fare;
        $vehicle_category->save();

        Toastr::success(translate('messages.vehicle_category_updated_successfully'));
        return redirect()->route('admin.ride.vehicle-category.index');
    }

    /**
     * Update status the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RideCategory  $vehicle_category
     * @return \Illuminate\Http\Response
     */
    public function update_status(RideCategory $vehicle_category, $status)
    {
        $vehicle_category->status = $status;
        $vehicle_category->save();
        Toastr::success(translate('messages.vehicle_category_status_updated_successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RideCategory  $vehicle_category
     * @return \Illuminate\Http\Response
     */
    public function destroy(RideCategory $vehicle_category)
    {
        $vehicle_category->delete();
        Toastr::success(translate('messages.vehicle_category_deleted_successfully'));
        return back();
    }
}
