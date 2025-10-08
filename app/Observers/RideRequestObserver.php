<?php

namespace App\Observers;

use App\CentralLogics\Helpers;
use App\CentralLogics\RideSharingLogics;
use App\Models\RideRequest;
use Illuminate\Support\Facades\DB;

class RideRequestObserver
{
    /**
     * Handle the RideRequest "created" event.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return void
     */
    public function created(RideRequest $rideRequest)
    {
        info("ride_requested_created_for_id: {$rideRequest->id}");
        $rideRequest->history_logs()->insert([
            'ride_request_id' => $rideRequest->id,
            'event' => $rideRequest->ride_status
        ]);
        $rideRequest->ongoing_log()->insert([
            'ride_request_id' => $rideRequest->id,
            'updated_at' => now(),
            'created_at' => now(),
        ]);
        if ($rideRequest->rider != null) {
            $delivery_man = $rideRequest->rider;
            $data = [
                'title' => translate('messages.new_ride_sharing_request_placed'),
                'description' => translate('messages.new_order_push_description'),
                'order_id' => $rideRequest->id,
                'image' => '',
            ];
            Helpers::send_push_notif_to_device($delivery_man->fcm_token, $data);
            DB::table('user_notifications')->insert([
                'data' => json_encode($data),
                'delivery_man_id' => $delivery_man->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $ride_sharing_logics = new RideSharingLogics();
            $ride_sharing_logics->send_rider_notification($rideRequest);
        }
    }

    /**
     * Handle the RideRequest "updated" event.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return void
     */
    public function updated(RideRequest $rideRequest)
    {
        info("ride_requested_updated_for_id: {$rideRequest->id}");
        $ride_sharing_logics = new RideSharingLogics();
        $rideRequest->history_logs()->insert([
            'ride_request_id' => $rideRequest->id,
            'event' => $rideRequest->ride_status
        ]);
        if (in_array($rideRequest->ride_status, ['failed', 'completed', 'canceled'])) {
            $rideRequest->ignore_logs()->delete();
            $rideRequest->ongoing_log()->delete();
            if ($rideRequest->ride_status == 'completed') {
                $ride_sharing_logics->create_transaction($rideRequest);
            }
        }

        $ride_sharing_logics->send_customer_notification($rideRequest->customer->cm_firebase_token, $rideRequest->id, $rideRequest->ride_status, $rideRequest->delivery_man_id);
    }

    /**
     * Handle the RideRequest "deleted" event.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return void
     */
    public function deleted(RideRequest $rideRequest)
    {
        info("ride_requested_deleted_for_id: {$rideRequest->id}");
    }

    /**
     * Handle the RideRequest "restored" event.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return void
     */
    public function restored(RideRequest $rideRequest)
    {
        info('ride_requested_restored');
    }

    /**
     * Handle the RideRequest "force deleted" event.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return void
     */
    public function forceDeleted(RideRequest $rideRequest)
    {
        info('ride_requested_forceDeleted');
    }
}
