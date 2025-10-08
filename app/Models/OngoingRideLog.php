<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OngoingRideLog extends Model
{
    use HasFactory;

    public function ride_request()
    {
        return $this->belongsTo(RideRequest::class);
    }
}
