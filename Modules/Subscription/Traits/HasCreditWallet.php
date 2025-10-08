<?php

namespace Modules\Subscription\Traits;

use Modules\Subscription\Entities\CreditTransaction;
use Modules\Subscription\Entities\CreditWallet;

trait HasCreditWallet
{
    public function creditWallet()
    {
        return $this->morphOne(CreditWallet::class, 'user')->withDefault();
    }

    public function getCreditWalletAndEnsuredSaved()
    {
        $creditWallet = $this->creditWallet;
        if ($creditWallet->id == null) {
            $creditWallet->save();
        }
        return $creditWallet;
    }

    public function getCreditBalanceAttribute()
    {
        return $this->creditWallet ? $this->creditWallet->balance : 0;
    }

    /**
     * Get all credit transactions for the user.
     * Uses polymorphic relationship - CreditTransaction belongs to user via morphTo
     */
    public function creditTransactions()
    {
        return $this->morphMany(CreditTransaction::class, 'user');
    }
}
