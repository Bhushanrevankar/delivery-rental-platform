<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'balance',
    ];

    protected $casts = ['balance' => 'decimal:2'];

    protected $attributes = ['balance' => 0];

    public function user()
    {
        return $this->morphTo();
    }
}
