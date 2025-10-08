<?php

namespace App\Observers;

use App\Models\DeliveryMan;
use App\Models\User;
use App\Models\Vendor;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User|DeliveryMan|Vendor $user)
    {
        $user->getCreditWalletAndEnsuredSaved();
    }
}
