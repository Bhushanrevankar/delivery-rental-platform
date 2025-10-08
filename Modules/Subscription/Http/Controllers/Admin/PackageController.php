<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\SubscriptionPackage;
use Brian2694\Toastr\Facades\Toastr;
use Modules\Subscription\Http\Requests\PackageRequest;
use App\Models\Translation;

class PackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::latest()->paginate(10);
        return view('subscription::admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('subscription::admin.packages.create');
    }

    public function store(PackageRequest $request)
    {
        $package = new SubscriptionPackage();
        $package->package_name = $request->package_name;
        $package->user_type = $request->user_type;
        $package->price = $request->price;
        $package->validity = $request->validity;
        $package->credits = $request->credits;
        $package->members_included = $request->members_included;
        $package->status = $request->status;
        $package->save();

        $translation = new Translation();
        $translation->translationable_type = 'App\Models\SubscriptionPackage';
        $translation->translationable_id = $package->id;
        $translation->locale = app()->getLocale();
        $translation->key = 'package_name';
        $translation->value = $request->package_name;
        $translation->save();

        Toastr::success('Subscription package created successfully!');
        return redirect()->route('admin.users.subscription.packages.index');
    }

    public function edit(SubscriptionPackage $package)
    {
        return view('subscription::admin.packages.edit', compact('package'));
    }

    public function update(PackageRequest $request, SubscriptionPackage $package)
    {
        $package->package_name = $request->package_name;
        $package->user_type = $request->user_type;
        $package->price = $request->price;
        $package->validity = $request->validity;
        $package->credits = $request->credits;
        $package->members_included = $request->members_included;
        $package->status = $request->status;
        $package->save();

        $translation = Translation::where('translationable_type', 'App\Models\SubscriptionPackage')
            ->where('translationable_id', $package->id)
            ->where('locale', app()->getLocale())
            ->where('key', 'package_name')
            ->first();

        if (!$translation) {
            $translation = new Translation();
            $translation->translationable_type = 'App\Models\SubscriptionPackage';
            $translation->translationable_id = $package->id;
            $translation->locale = app()->getLocale();
            $translation->key = 'package_name';
        }

        $translation->value = $request->package_name;
        $translation->save();

        Toastr::success('Subscription package updated successfully!');
        return redirect()->route('admin.users.subscription.packages.index');
    }

    public function status($id, $status)
    {
        $package = SubscriptionPackage::find($id);
        $package->status = $status;
        $package->save();
        Toastr::success('Package status updated!');
        return back();
    }

    public function destroy(SubscriptionPackage $package)
    {
        $package->delete();
        Toastr::success('Subscription package deleted successfully!');
        return back();
    }
}
