<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBringfixSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_bringfix_subscription_id',
        'day_of_week',
        'time_slot',
    ];

    public function userBringfixSubscription()
    {
        return $this->belongsTo(UserBringfixSubscription::class);
    }

    protected static function newFactory()
    {
        // return \\Modules\\Subscription\\Database\\factories\\UserBringfixScheduleFactory::new();
    }
}
