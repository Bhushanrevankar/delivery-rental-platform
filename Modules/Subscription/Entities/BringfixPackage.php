<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BringfixPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trips_per_month',
        'max_distance_km',
        'trip_type',
        'price',
        'status',
    ];
    
    protected static function newFactory()
    {
        // return \Modules\Subscription\Database\factories\BringfixPackageFactory::new();
    }
}
