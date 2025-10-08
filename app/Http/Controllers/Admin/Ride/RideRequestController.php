<?php

namespace App\Http\Controllers\Admin\Ride;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use Illuminate\Http\Request;

class RideRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $zone_ids = $request->query('zone', []);
        $vendor_ids = $request->query('vendor', []);
        $ride_requeststatus = $request->query('orderStatus', []);
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');
        $scheduled = $request->query('scheduled');

        $ride_requests = RideRequest::when(count($zone_ids) > 0, function ($query) use ($zone_ids) {
            $query->whereIn('zone_id', $zone_ids);
        })
            ->when(count($vendor_ids) > 0, function ($query) use ($vendor_ids) {
                $query->whereIn('vehicle_category_id', $vendor_ids);
            })
            ->when(count($ride_requeststatus) > 0, function ($query) use ($ride_requeststatus) {
                $query->whereIn('ride_status', $ride_requeststatus);
            })
            ->when($from_date && $to_date, function ($query) use ($from_date, $to_date) {
                $query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
            })
            ->when($scheduled, function ($query) {
                $query->where('schedule_at', '<>', null);
            })
            ->latest()->paginate(config('default_pagination'));
        
        return view('admin-views.ride-sharing.request.list', compact('ride_requests', 'zone_ids', 'vendor_ids', 'ride_requeststatus', 'from_date', 'to_date', 'scheduled'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $rideRequest = RideRequest::with(['rider', 'customer'])->findOrFail($id);
        // dd($rideRequest);
        return view('admin-views.ride-sharing.request.view', compact('rideRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(RideRequest $rideRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RideRequest  $rideRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RideRequest $rideRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(RideRequest $rideRequest)
    {
        //
    }
}
