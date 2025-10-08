<?php

namespace App\CentralLogics;

use App\Models\Admin;
use App\Models\AdminWallet;
use Exception;
use App\Models\DeliveryMan;
use App\Models\DeliveryManWallet;
use App\Models\RideCategory;
use App\Models\IgnoreRideLog;
use App\Models\OngoingRideLog;
use App\Models\RideLogHistory;
use App\Models\RideRequest;
use App\Models\RideTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use KMLaravel\GeographicalCalculator\Facade\GeographicalCalculatorFacade;

class RideSharingLogics
{

    public function __invoke()
    {
        $this->check_pending_request();
    }

    public function calculate_fare(RideCategory $ride_category, float $distance, float $actual_time, float $estimated_time)
    {
        return round(($ride_category->base_fare + ($ride_category->per_km_fare * $distance) + (max($actual_time - $estimated_time, 0) * $ride_category->per_min_waiting_fare)), config('round_up_to_digit'));
    }

    public function send_rider_notification(RideRequest $ride_request)
    {
        // info("ride_request_notification_send_for__{$ride_request_id}__|__ride_request_{$zone_id}_{$ride_category_id}");

        $data = [
            'title' => translate('messages.new_ride_sharing_request_placed'),
            'description' => translate('messages.new_order_push_description'),
            'order_id' => $ride_request->id,
            'type' => 'ride_request',
            'image' => '',
        ];

        $fcm_tokens = Helpers::query_nearest_deliveryman(
            zone_id: $ride_request->zone_id,
            start_lat: $ride_request->pickup_point->latitude,
            start_long: $ride_request->pickup_point->longitude,
            vehicle_id: $ride_request->vehicle_category->dm_vehicle_id,
            credits_required: $ride_request->driver_credits_required,
        )->pluck('fcm_token')->toArray();
        Log::info('fcm_tokens queried for ride request - delivery', ['fcm_tokens' => $fcm_tokens]);

        // TODO: send push notification in queue.
        foreach ($fcm_tokens as $fcm_token) {
            Helpers::send_push_notif_to_device($fcm_token, $data);
        }

        // info(Helpers::send_push_notif_to_topic($data, "ride_request_{$zone_id}_{$ride_category_id}", 'ride_request'));
        // if(!$rider = $this->get_nearest_rider($ride_request_id, $zone_id, $ride_category_id)){
        //     return false;
        // }
        // OngoingRideLog::updateOrInsert(['ride_request_id'=>$ride_request_id],['delivery_man_id'=>$rider->id, 'updated_at'=>now()]);
        // RideLogHistory::insert(['ride_request_id'=>$ride_request_id, 'rider_id'=>$rider->id,'event'=>'requested']);
        // $data = [
        //     'title' => translate('messages.new_ride_sharing_request_placed'),
        //     'description' => '',
        //     'order_id' => $ride_request_id,
        //     'image' => '',
        //     'type' => 'ride_request',
        // ];
        // try{
        //     Helpers::send_push_notif_to_device($rider->fcm_token, $data);
        //     info(["ride_request_notification_send_for__{$ride_request_id}", $data]);
        // }catch(Exception $ex){
        //     info(['rider_request_notification_error',$ex->getMessage()]);
        // }
        // Helpers::send_push_notif_to_device($rider->fcm_token, $data);

    }

    public function send_customer_notification($cm_firebase_token, $ride_request_id, $status, $rider_id = null)
    {
        $data = [
            'title' => translate('messages.ride_request_status_updated_tile', ['status' => translate("messages.{$status}")]),
            'description' => '',
            'order_id' => $ride_request_id,
            'rider_id' => $rider_id,
            'image' => '',
            'type' => "ride_{$status}",
        ];
        try {
            Helpers::send_push_notif_to_device($cm_firebase_token, $data);
            info(["customer_ride_request_notification_send_for__{$ride_request_id}", $cm_firebase_token, $data]);
        } catch (Exception $ex) {
            info(['customer_ride_request_notification_error__', $ex->getMessage()]);
        }
    }

    public function create_ignore_log($ride_request_id, $rider_id, $zone_id, $ride_category_id)
    {
        try {
            DB::beginTransaction();
            $ignoreLog = new IgnoreRideLog();
            $ignoreLog->ride_request_id = $ride_request_id;
            $ignoreLog->delivery_man_id = $rider_id;
            $ignoreLog->save();
            $rideLog = new RideLogHistory();
            $rideLog->ride_request_id = $ride_request_id;
            $rideLog->rider_id = $rider_id;
            $rideLog->event = 'ignored';
            $rideLog->save();
            DB::commit();
            $this->send_rider_notification($ride_request_id, $zone_id, $ride_category_id);
            return true;
        } catch (Exception $ex) {
            info(['create_ignore_log_error____', $ex->getMessage()]);
            DB::rollback();
        }
        return false;
    }

    private function get_nearest_rider($ride_request_id, $zone_id, $ride_category_id): ?DeliveryMan
    {
        $database = app('firebase.database');
        $reference = $database->getReference("zone_{$zone_id}/vc_{$ride_category_id}");
        $dm_list = $reference->getValue();
        $rideRequest = \App\Models\RideRequest::whereId($ride_request_id)->first('pickup_point');
        if (!$dm_list || !$rideRequest) return null;

        $dm_ids = array_column(array_filter($dm_list, function ($var) {
            return $var['isAvailable'];
        }), 'latLng', 'id');
        info([count($dm_ids) . " riders_found", $dm_ids]);
        $nearest_neighbours = GeographicalCalculatorFacade::setPoint([$rideRequest->pickup_point->latitude, $rideRequest->pickup_point->longitude])->setPoints($dm_ids)->getOrderByNearestNeighbor();
        info(['nearest_neighbours___', $nearest_neighbours]);
        foreach ($nearest_neighbours as $key => $dms) {
            info("dm_coordinates___", $dms);
            if ($key != 0) {
                unset($dms['key']);
                $dm_id = array_search($dms, $dm_ids);
                info("dm_id____{$dm_id}");
                if ($dm = DeliveryMan::rider()->active()->availableRider($ride_request_id, $zone_id, $ride_category_id)->whereId($dm_id)->first()) {
                    // if($dm = DeliveryMan::rider()->active()->availableRider($ride_request_id, $zone_id, $ride_category_id)->whereId($dm_id)->first()){
                    info("dm_found____");
                    return $dm;
                }
            }
        }
        // while(count($dm_ids)){
        //     try{
        //         info(['dm_ids_____', array_values($dm_ids)]);
        //         $closest = GeoFacade::setMainPoint([$rideRequest->pickup_point->getLat(),$rideRequest->pickup_point->getLng()])->setPoints(array_values($dm_ids))->getClosest();
        //         $dm_id = array_search(array_values($closest)[0], $dm_ids);
        //         info("dm_id____{$dm_id}");
        //         unset($dm_ids[$dm_id]);
        //         if(count($dm_ids) == 1)
        //         {
        //             $dm_id = array_keys($dm_ids)[0];
        //             unset($dm_ids[$dm_id]);
        //         }
        //         if($dm = DeliveryMan::rider()->whereId($dm_id)->first()){
        //             return $dm;
        //         }
        //     }catch(Exception $ex){
        //         info(['get_nearest_error___', $ex->getMessage()]);
        //         break;
        //     }

        // }
        return null;
    }

    public function check_pending_request()
    {
        $ongoing_requests = OngoingRideLog::with('ride_request')->take(10)->get();
        info(count($ongoing_requests) . " requests_found");
        foreach ($ongoing_requests as $ongoing_request) {
            $this->send_rider_notification($ongoing_request->ride_request->id, $ongoing_request->ride_request->ride_zone_id, $ongoing_request->ride_request->ride_category_id);
        }
    }

    public function create_transaction(RideRequest $ride_request): void
    {
        $comission = \App\Models\BusinessSetting::where('key', 'ride_sharing_admin_commission')->first();
        $comission = isset($comission) ? $comission->value : 0;
        $rider_commission = $comission ? ($ride_request->actual_fare / 100) * $comission : 0;

        RideTransaction::insert([
            'delivery_man_id' => $ride_request->delivery_man_id,
            'ride_request_id' => $ride_request->id,
            'total_fare' => $ride_request->total_fare,
            'tax' => $ride_request->tax,
            'rider_commission' => $rider_commission,
            'admin_commission' => $ride_request->actual_fare - $rider_commission,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $adminWallet = AdminWallet::firstOrNew(
            ['admin_id' => Admin::where('role_id', 1)->first()->id]
        );

        $dmWallet = DeliveryManWallet::firstOrNew(
            ['delivery_man_id' => $ride_request->delivery_man_id]
        );
        $dmWallet->collected_cash = $dmWallet->collected_cash + $ride_request->total_fare;

        $dmWallet->total_earning = $dmWallet->total_earning + $rider_commission;
        try {
            DB::beginTransaction();
            $dmWallet->save();
            $adminWallet->save();
            DB::commit();
            info("ride_request_transaction_created_{$ride_request->id}");
            return;
        } catch (Exception $ex) {
            DB::rollBack();
            info(["ride_request_transaction_error_{$ride_request->id}", $ex->getMessage()]);
            return;
        }
    }
}
