<?php

namespace Modules\Subscription\Http\Controllers\Admin;

use App\Services\WalletService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Subscription\Entities\Subscription;

class SubscriptionController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $subscriptions = Subscription::with(['subscriber', 'package'])->latest()->paginate(10);
        return view('subscription::admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Subscription $subscription)
    {
        DB::beginTransaction();

        $credits_to_deduct = $subscription->remaining_credits;

        $this->walletService->deductCredits(
            user: $subscription->subscriber,
            amount: $credits_to_deduct,
            transaction_type: 'deduction',
            reference: 'sub:' . $subscription->id,
            details: 'Deleted ' .  $subscription->package?->package_name ?? 'Unknown' . ' subscription plan',
        );

        $subscription->delete();

        DB::commit();

        return back()->with('success', 'Subscription cancelled successfully.');
    }
}
