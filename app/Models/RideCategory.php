<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideCategory extends Model
{
    use HasFactory;
    
    protected $casts = [
        "base_fare"=>'float',
        "per_km_fare"=> 'float',
        "per_min_waiting_fare"=> 'float',
        "status"=> 'integer'
    ];

    public function dmVehicle()
    {
        return $this->belongsTo(DMVehicle::class, 'dm_vehicle_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
