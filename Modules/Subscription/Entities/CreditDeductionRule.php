<?php

namespace Modules\Subscription\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditDeductionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_type',
        'module_id',
        'condition_type',
        'min_value',
        'max_value',
        'credits_to_deduct',
        'status',
    ];

    protected $casts = [
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'credits_to_deduct' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(\App\Models\Module::class, 'module_id');
    }

    protected static function newFactory()
    {
        // return \Modules\Subscription\Database\factories\CreditDeductionRuleFactory::new();
    }
}
