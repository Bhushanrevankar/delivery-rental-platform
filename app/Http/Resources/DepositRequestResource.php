<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DepositRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'owner_type' => $this->owner_type,
            'owner_id' => $this->owner_id,
            'amount' => $this->amount,
            'payment_channel' => $this->payment_channel,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'proof_img_url' => $this->proof_img_url,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'wallet_transaction_id' => $this->wallet_transaction_id,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
