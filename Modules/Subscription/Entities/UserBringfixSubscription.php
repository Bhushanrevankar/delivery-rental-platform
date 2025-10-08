<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBringfixSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bringfix_package_id',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'dropoff_address',
        'dropoff_lat',
        'dropoff_lng',
        'route_distance_km',
        'trips_total',
        'trips_remaining',
        'expiry_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function bringfixPackage()
    {
        return $this->belongsTo(BringfixPackage::class);
    }

    public function schedules()
    {
        return $this->hasMany(UserBringfixSchedule::class);
    }

    protected static function newFactory()
    {
        // return \\Modules\\Subscription\\Database\\factories\\UserBringfixSubscriptionFactory::new();
    }
}
