<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class RideRequest extends Model
{
    use HasFactory, HasSpatial;

    protected $casts = [
        'user_id' => 'integer',
        'delivery_man_id' => 'integer',
        'ride_category_id' => 'integer',
        'zone_id' => 'integer',
        'estimated_fare' => 'float',
        'actual_fare' => 'float',
        'estimated_distance' => 'float',
        'actual_distance' => 'float',
        'total_fare' => 'float',
        'tax' => 'float',
        'otp' => 'integer',
        'pickup_point' => Point::class,
        'dropoff_point' => Point::class,
        'actual_pickup_point' => Point::class,
        'actual_dropoff_point' => Point::class,
        'customer_credits_required' => 'decimal:2',
        'driver_credits_required' => 'decimal:2',
    ];

    protected $attributes = [
        'customer_credits_required' => 0,
        'driver_credits_required' => 0,
        'customer_credits_status' => 'none',
        'driver_credits_status' => 'none',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rider()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_man_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function vehicle_category()
    {
        return $this->belongsTo(RideCategory::class, 'ride_category_id');
    }

    public function ongoing_log()
    {
        return $this->hasOne(OngoingRideLog::class);
    }

    public function history_logs()
    {
        return $this->hasMany(RideLogHistory::class);
    }

    public function ignore_logs()
    {
        return $this->hasMany(IgnoreRideLog::class);
    }

    public function rider_last_location()
    {
        return $this->rider->last_location();
    }
}
