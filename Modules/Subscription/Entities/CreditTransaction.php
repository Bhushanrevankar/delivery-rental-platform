<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class CreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'amount',
        'transaction_type',
        'reference_id',
        'reference_type',
        'details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->morphTo();
    }

    public function reference()
    {
        return $this->morphTo();
    }

    protected static function newFactory()
    {
        return \Modules\Subscription\Database\factories\CreditTransactionFactory::new();
    }
}
