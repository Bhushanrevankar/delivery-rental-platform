<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\Entities\BringfixPackage;
use Brian2694\Toastr\Facades\Toastr;

class BringfixPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $packages = BringfixPackage::latest()->paginate(10);
        return view('subscription::admin.bringfix_packages.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('subscription::admin.bringfix_packages.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'trips_per_month' => 'required|integer',
            'max_distance_km' => 'required|numeric',
            'trip_type' => 'required|in:one_way,two_way',
            'price' => 'required|numeric',
            'status' => 'required|boolean',
        ]);

        BringfixPackage::create($request->all());

        return redirect()->route('admin.users.subscription.bringfix.packages.index')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('subscription::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(BringfixPackage $bringfixPackage)
    {
        return view('subscription::admin.bringfix_packages.edit', compact('bringfixPackage'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, BringfixPackage $bringfixPackage)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'trips_per_month' => 'required|integer',
            'max_distance_km' => 'required|numeric',
            'trip_type' => 'required|in:one_way,two_way',
            'price' => 'required|numeric',
            'status' => 'required|boolean',
        ]);

        $bringfixPackage->update($request->all());

        return redirect()->route('admin.users.subscription.bringfix.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(BringfixPackage $bringfixPackage)
    {
        $bringfixPackage->delete();
        return redirect()->route('admin.users.subscription.bringfix.packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    public function status($id, $status)
    {
        $package = BringfixPackage::find($id);
        $package->status = $status;
        $package->save();
        Toastr::success('Package status updated!');
        return back();
    }
}
