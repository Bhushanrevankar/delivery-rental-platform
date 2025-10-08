<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class RideZone extends Model
{
    use HasFactory, HasSpatial;

    protected $casts = [
        "status" => 'integer',
        'coordinates' => Point::class
    ];

    public function riders()
    {
        return $this->belongsToMany(DeliveryMan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
}
