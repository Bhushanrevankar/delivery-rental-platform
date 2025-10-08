<?php

namespace App\Http\Controllers\Admin\Ride;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\RideRequest;
use App\Models\RideTransaction;
use App\Models\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RideSharingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $settings =  array_column(BusinessSetting::whereIn('key', ['ride_sharing_admin_commission', 'ride_sharing_tax'])->get()->toArray(), 'value', 'key');
        return view('admin-views.ride-sharing.settings', compact('settings'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'ride_sharing_admin_commission' => 'required',
            'ride_sharing_tax' => 'required'
        ]);
        // BusinessSetting::updateOrInsert(['key' => 'ride_sharing_status'], [
        //     'value' => $request->ride_sharing_status??0
        // ]);
        BusinessSetting::updateOrInsert(['key' => 'ride_sharing_admin_commission'], [
            'value' => $request->ride_sharing_admin_commission
        ]);
        BusinessSetting::updateOrInsert(['key' => 'ride_sharing_tax'], [
            'value' => $request->ride_sharing_tax
        ]);
        Toastr::success(translate('Ride sharing settings updated.'));
        return back();
    }

    public function report(Request $request)
    {
        $from = $request->query('from_date', now()->firstOfMonth()->format('Y-m-d'));
        $to = $request->query('to_date', now()->endOfMonth()->format('Y-m-d'));
        $zone_id = $request->query('zone_id', 'all');
        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;
        $transactions = RideTransaction::when(isset($zone), function ($query) use ($zone) {
            return $query->where('zone_id', $zone->id);
        })
            ->when(request('module_id'), function ($query) {
                return $query->module(request('module_id'));
            })
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->latest();

        $data['admin_earned'] = $transactions->sum('admin_commission');
        $data['rider_earned'] = $transactions->sum('rider_commission');
        $data['total_sell'] = $transactions->sum('total_fare');

        $ride_requests = RideRequest::select(DB::raw('ride_status, count(*) as request_count'))->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->groupBy('ride_status')->pluck('request_count', 'ride_status');
        $data['status_count'] = $ride_requests->toArray();
        $in_progress = 0;
        foreach ($data['status_count'] as $key => $status) {
            if (!in_array($key, ['failed', 'completed', 'canceled'])) {
                $in_progress += $status;
            }
        }
        // $data['status_count']['total']=array_sum($ride_requests->toArray());
        $data['status_count']['in_progress'] = $in_progress;

        $transactions = $transactions->paginate(config('default_pagination'))->withQueryString();

        return view('admin-views.ride-sharing.report', compact('from', 'to', 'zone_id', 'zone', 'transactions', 'data'));
    }
}
