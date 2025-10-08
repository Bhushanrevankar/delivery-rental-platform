<?php

namespace App\Http\Controllers\Admin\Ride;

use App\CentralLogics\Helpers;
use App\Models\RideZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;

class RideZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $zones = RideZone::withCount(['riders'])->latest()->paginate(config('default_pagination'));
        return view('admin-views.ride-sharing.zone.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        // dd(Zone::intersects('coordinates', new Polygon([new LineString($polygon)]))->count());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:ride_zones|max:191',
            'coordinates' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $value = $request->coordinates;
        foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
            if ($index == 0) {
                $lastcord = explode(',', $single_array);
            }
            $coords = explode(',', $single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }

        $polygon[] = new Point($lastcord[0], $lastcord[1]);
        $zone = new RideZone();
        $zone->name = $request->name;
        $zone->coordinates = new Polygon([new LineString($polygon)]);
        $zone->save();

        return response()->json([], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RideZone  $rideZone
     * @return \Illuminate\Http\Response
     */
    public function show(RideZone $rideZone)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RideZone  $rideZone
     * @return \Illuminate\Http\Response
     */
    public function edit(RideZone $zone)
    {
        $zone = RideZone::selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->whereId($zone->id)->first();
        return view('admin-views.ride-sharing.zone.edit', compact('zone'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RideZone  $rideZone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RideZone $zone)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191|unique:ride_zones,name,' . $zone->id,
            'coordinates' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $value = $request->coordinates;
        foreach (explode('),(', trim($value, '()')) as $index => $single_array) {
            if ($index == 0) {
                $lastcord = explode(',', $single_array);
            }
            $coords = explode(',', $single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }

        $polygon[] = new Point($lastcord[0], $lastcord[1]);

        $zone->name = $request->name;
        $zone->coordinates = new Polygon([new LineString($polygon)]);
        $zone->save();

        return response()->json([], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RideZone  $rideZone
     * @return \Illuminate\Http\Response
     */
    public function destroy(RideZone $rideZone)
    {
        $rideZone->delete();
        Toastr::success(translate('messages.ride_sharing_zone_deleted_successfully'));
        return back();
    }

    public function get_zone_cordinates($id = 0)
    {
        $zones = RideZone::where('id', '<>', $id)->active()->get();
        $data = [];
        foreach ($zones as $zone) {
            $data[] = Helpers::format_coordiantes($zone->coordinates[0]);
        }
        return response()->json($data, 200);
    }

    public function status(Request $request)
    {
        $zone = RideZone::findOrFail($request->id);
        $zone->status = $request->status;
        $zone->save();
        Toastr::success(translate('messages.ride_sharing_zone_status_updated'));
        return back();
    }
}
