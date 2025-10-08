<?php

namespace Modules\Subscription\Traits;

use Modules\Subscription\Entities\Subscription;

trait HasSubscription
{
    /**
     * Get all of the subscriptions for the model.
     */
    public function subscriptions()
    {
        return $this->morphMany(Subscription::class, 'subscriber');
    }
}
