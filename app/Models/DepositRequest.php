<?php

namespace App\Models;

use App\CentralLogics\CustomerLogic;
use App\Models\Constants\WalletRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

class DepositRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'status', 'approved_at', 'wallet_transaction_id'];

    protected $casts = [
        'amount' => 'float',
        'approved_at' => 'datetime',
    ];

    protected $attributes = ['status' => WalletRequestStatus::pending];

    public function approve(): bool
    {
        if ($this->status != WalletRequestStatus::pending) {
            return false;
        }

        DB::beginTransaction();

        $owner = $this->owner;

        if ($owner instanceof User) {
            $walletTransaction = CustomerLogic::create_wallet_transaction($owner->id, $this->amount, 'add_fund_by_admin', null);

            if ($walletTransaction == false) return false;

            $this->walletTransaction()->associate($walletTransaction);
            $this->status = WalletRequestStatus::approved;
            $this->approved_at = now();
            $this->remarks = "Approved by admin.";
            $this->save();

            DB::commit();
            return true;
        }

        DB::rollBack();
        return false;
    }

    public function reject(?string $remarks): bool
    {
        if ($this->status != WalletRequestStatus::pending) {
            return false;
        }

        $this->remarks = $remarks;
        $this->status = WalletRequestStatus::rejected;
        $this->save();
        return true;
    }

    public function cancel(?string $remarks): bool
    {
        if ($this->status != WalletRequestStatus::pending) {
            return false;
        }

        $this->remarks = $remarks;
        $this->status = WalletRequestStatus::canceled;
        $this->save();
        return true;
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
