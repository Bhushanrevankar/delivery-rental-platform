<?php

namespace Modules\Subscription\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CreditWalletController extends Controller
{
    /**
     * Fetch the user's current credit wallet balance.
     */
    public function get_wallet(Request $request)
    {
        $user = $request->user();

        // Sum the remaining credits from all active subscriptions for the user
        // $total_credits = Subscription::where('subscriber_id', $user->id)
        //     ->where('subscriber_type', get_class($user))
        //     ->where('status', 1)
        //     ->where('expiry_date', '>=', now())
        //     ->sum('remaining_credits');

        // $wallet_data = [
        //     'balance' => (string)round($total_credits, 2),
        //     'currency_symbol' => Helpers::currency_symbol(),
        //     'last_updated' => now()->toIso8601String()
        // ];

        return response()->json($user->getCreditWalletAndEnsuredSaved(), 200);
    }

    /**
     * Fetch the user's credit transactions with pagination.
     */
    public function get_transactions(Request $request)
    {
        $user = $request->user();

        // $transactions = CreditTransaction::where('user_id', $user->id)
        //     ->where('user_type', get_class($user))
        //     ->latest()
        //     ->paginate($request->limit ?? 10);

        return response()->json($user->creditTransactions()->latest()->paginate($request->limit ?? 10), 200);
    }
}
