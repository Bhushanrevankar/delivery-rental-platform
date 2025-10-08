<?php

namespace Modules\Subscription\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\SubscriptionPackage;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user(); // Works with specific guard middleware (auth:api, auth:dm-api, auth:vendor-api)

        // Determine user type from model class
        $user_type = match (true) {
            $user instanceof \App\Models\DeliveryMan => 'driver',
            $user instanceof \App\Models\Vendor => 'merchant',
            default => 'customer'
        };

        $packages = SubscriptionPackage::where('user_type', $user_type)
            ->where('status', 1)
            ->get();

        return response()->json($packages);
    }

    /**
     * Actual purchase method for subscription package.
     * @param Request $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:subscription_packages,id',
        ]);

        $package = SubscriptionPackage::find($request->package_id);
        /** @var \App\Models\User|\App\Models\DeliveryMan|\App\Models\Store $user */
        $user = Auth::user(); // Works with specific guard middleware (auth:api, auth:dm-api, auth:vendor-api)

        // ** TODO: INTEGRATE PAYMENT GATEWAY HERE **
        // 1. Initiate payment process with $package->price
        // 2. On successful payment, proceed with the code below.
        // 3. If payment fails, return an error response.

        // Assuming payment is successful:

        // Add new subscription entry
        $subscription = $user->subscriptions()->create([
            'package_id' => $package->id,
            'expiry_date' => Carbon::now()->addDays($package->validity),
            'total_credits' => $package->credits,
            'remaining_credits' => $package->credits,
            'status' => true,
        ]);

        // Add credits to wallet
        $this->walletService->addCredits(
            user: $user,
            amount: $package->credits,
            transaction_type: 'purchase',
            reference: 'sub:' . $subscription->id,
            details: 'Purchased ' .  $package->package_name . ' plan',
        );

        return response()->json([
            'message' => 'Subscription purchased successfully!',
            'subscription' => $subscription
        ], 200);
    }

    /**
     * Get current subscriptions for authenticated user.
     */
    public function list()
    {
        $user = Auth::user();

        $subscriptions = $user->subscriptions()
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($subscriptions);
    }

    /**
     * Get latest subscription for authenticated user.
     */
    public function latest()
    {
        $user = Auth::user();

        $subscription = $user->subscriptions()
            ->with('package')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No subscription found'
            ], 404);
        }

        return response()->json($subscription);
    }
}
