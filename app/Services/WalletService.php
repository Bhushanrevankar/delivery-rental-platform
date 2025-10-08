<?php

namespace App\Services;

use App\Models\DeliveryMan;
use App\Models\User;
use App\Models\Vendor;
use Modules\Subscription\Entities\CreditTransaction;
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\Subscription\Entities\Subscription;

class WalletService
{
    /**
     * Add credits to a user's wallet.
     *
     * @param User $user
     * @param float $amount
     * @param string $transaction_type
     * @param Model|null $reference
     * @param string|null $details
     * @return CreditTransaction
     * @throws Exception
     */
    public function addCredits(User|DeliveryMan|Vendor $user, float $amount, string $transaction_type, $reference = null, ?string $details = null): CreditTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Credit amount must be positive.");
        }

        $wallet = $user->getCreditWalletAndEnsuredSaved();

        return DB::transaction(function () use ($wallet, $user, $amount, $transaction_type, $reference, $details) {
            $wallet->increment('balance', $amount);

            $transaction = new CreditTransaction([
                'amount' => $amount,
                'transaction_type' => $transaction_type,
                'details' => $details,
            ]);

            $transaction->user()->associate($user);

            if ($reference) {
                $transaction->reference()->associate($reference);
            }

            $transaction->save();

            return $transaction;
        });
    }

    /**
     * Deduct credits from a user's wallet.
     *
     * @param User $user
     * @param float $amount
     * @param string $transaction_type
     * @param Model|null $reference
     * @param string|null $details
     * @return CreditTransaction
     * @throws Exception
     */
    public function deductCredits(User|DeliveryMan|Vendor $user, float $amount, string $transaction_type, $reference = null, ?string $details = null): CreditTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Debit amount must be positive.");
        }

        $wallet = $user->getCreditWalletAndEnsuredSaved();

        if (!$wallet || $wallet->balance < $amount) {
            throw new Exception("Insufficient credit balance.");
        }

        $subscriptions = Subscription::where('subscriber_id', $user->id)
            ->where('subscriber_type', get_class($user))
            ->where('status', 1)
            ->where('is_canceled', 0)
            ->where('expiry_date', '>=', now())
            ->orderBy('expiry_date', 'ASC')
            ->get();

        return DB::transaction(function () use ($wallet, $user, $amount, $transaction_type, $reference, $details, $subscriptions) {
            $wallet->decrement('balance', $amount);

            $remaining_amount_to_deduct = $amount;
            foreach ($subscriptions as $subscription) {
                $remaining_amount_from_subscription = $subscription->remaining_credits;
                if ($remaining_amount_from_subscription <= $remaining_amount_to_deduct) {
                    $remaining_amount_to_deduct -= $remaining_amount_from_subscription;
                    $subscription->update(['remaining_credits' => 0]);
                } else {
                    $subscription->update(['remaining_credits' => $remaining_amount_from_subscription - $remaining_amount_to_deduct]);
                    $remaining_amount_to_deduct = 0;
                }
                if ($remaining_amount_to_deduct <= 0) break;
            }

            $transaction = new CreditTransaction([
                'amount' => -$amount, // Store deductions as negative values
                'transaction_type' => $transaction_type,
                'details' => $details,
            ]);

            $transaction->user()->associate($user);

            if ($reference) {
                $transaction->reference()->associate($reference);
            }

            $transaction->save();

            return $transaction;
        });
    }
}
