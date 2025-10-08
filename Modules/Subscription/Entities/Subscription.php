<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SubscriptionPackage;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'expiry_date' => 'datetime',
        'status' => 'boolean',
        'is_trial' => 'boolean',
        'is_canceled' => 'boolean',
        'total_credits' => 'decimal:2',
        'remaining_credits' => 'decimal:2',
    ];

    public function subscriber()
    {
        return $this->morphTo();
    }

    public function package()
    {
        return $this->belongsTo(SubscriptionPackage::class, 'package_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Subscription\Database\factories\SubscriptionFactory::new();
    }
}
