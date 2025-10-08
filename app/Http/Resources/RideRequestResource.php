<?php

namespace App\Http\Resources;

use App\CentralLogics\Helpers;
use Illuminate\Http\Resources\Json\JsonResource;

class RideRequestResource extends JsonResource
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
            'ride_category' => $this->vehicle_category ? $this->vehicle_category->name : null,
            'zone' => $this->zone ? $this->zone->name : null,
            'ride_status' => $this->ride_status,
            'pickup_point' => $this->pickup_point,
            'pickup_address' => $this->pickup_address,
            'pickup_time' => $this->pickup_time,
            'dropoff_point' => $this->dropoff_point,
            'dropoff_address' => $this->dropoff_address,
            'dropoff_time' => $this->dropoff_time,
            'estimated_time' => $this->estimated_time,
            'estimated_fare' => $this->estimated_fare,
            'estimated_distance' => $this->estimated_distance,
            'actual_time' => $this->actual_time,
            'actual_fare' => $this->actual_fare,
            'actual_distance' => $this->actual_distance,
            'total_fare' => $this->total_fare,
            'tax' => $this->tax,
            'otp' => $this->otp,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'customer_id' => $this->user_id,
            'customer_name' => $this->relationLoaded('customer') && $this->customer ? $this->customer->f_name . ' ' . $this->customer->f_name : null,
            'customer_image' => $this->relationLoaded('customer') && $this->customer ? $this->customer->image : null,
            'rider' => $this->relationLoaded('rider') && $this->rider ? Helpers::deliverymen_data_formatting([$this->rider])[0] : null,
        ];
    }
}
