<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Subscription\Entities\UserBringfixSubscription;

class UserBringfixSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = UserBringfixSubscription::with(['user', 'bringfixPackage'])->latest()->paginate(10);
        return view('subscription::admin.user_bringfix_subscriptions.index', compact('subscriptions'));
    }

    public function show(UserBringfixSubscription $userBringfixSubscription)
    {
        $userBringfixSubscription->load(['user', 'bringfixPackage', 'schedules']);
        return view('subscription::admin.user_bringfix_subscriptions.show', compact('userBringfixSubscription'));
    }

    public function destroy(UserBringfixSubscription $userBringfixSubscription)
    {
        $userBringfixSubscription->delete();
        return redirect()->route('admin.users.subscription.bringfix.list.index')
            ->with('success', 'Subscription deleted successfully.');
    }
}
