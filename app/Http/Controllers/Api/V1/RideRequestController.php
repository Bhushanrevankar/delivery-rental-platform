<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Resources\RideRequestResource;
use App\Models\BusinessSetting;
use App\Models\DeliveryMan;
use App\Models\RideCategory;
use App\Models\RideRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MatanYadaev\EloquentSpatial\Objects\Point;
use App\Models\DMReview;
use App\Models\Module;
use App\Models\Zone;
use Illuminate\Support\Facades\Storage;
use Modules\Subscription\Entities\CreditDeductionRule;

class RideRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 1);
        $ride_requests = RideRequest::with(['customer', 'zone', 'vehicle_category', 'rider'])->where('user_id', $request->user()->id)->latest()->paginate($limit, '*', 'ride_request', $offset);
        // return($ride_requests->items()[0]);
        return response()->json([
            'limit' => $limit,
            'offset' => $offset,
            'total_size' => $ride_requests->total(),
            'data' => RideRequestResource::collection($ride_requests),
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ride_category(Request $request)
    {
        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 1);
        $ride_categories = RideCategory::active()->paginate($limit, '*', 'ride_category', $offset);
        return response()->json([
            'limit' => $limit,
            'offset' => $offset,
            'total_size' => $ride_categories->total(),
            'data' => $ride_categories->items(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_lat' => 'required',
            'pickup_lng' => 'required',
            'dropoff_lat' => 'required',
            'dropoff_lng' => 'required',
            'pickup_address' => 'required',
            'dropoff_address' => 'required',
            'estimated_time' => 'required',
            'estimated_distance' => 'required',
            'ride_category_id' => 'required',
            'rider_code' => 'nullable|string|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (RideRequest::where('user_id', $request->user()->id)->whereNotIn('ride_status', ['completed', 'canceled', 'failed'])->count() > 0) {
            return response()->json([
                'errors' => [['code' => 'already-exists', 'message' => translate('messages.please_complete_your_runnig_requests_first')]]
            ], 403);
        }

        $delivery_man = null;

        if ($request->rider_code != null) {
            // get delivery man first
            $delivery_man = DeliveryMan::where('code', $request->rider_code)
                ->available()
                ->active()
                ->where(function ($q) {
                    $q->where('earning', 0) // salary based
                        ->orWhere(function ($q) {
                            $now = now()->toDateString();
                            $q->whereDate('subscription_period_start', '<=', $now)
                                ->whereDate('subscription_period_end', '>=', $now);
                        });
                })
                ->first();
            if ($delivery_man == null) return response()->json(['message' => 'Deliveryman does not exists or not available at the moment.'], 400);

            if (
                $request->ride_category_id != null &&
                $delivery_man->ride_category_id != $request->ride_category_id
            ) {
                return response()->json(['message' => 'Deliveryman not applicable for selected vehicle type.'], 400);
            }
        }

        $pickup_point = new Point($request->pickup_lat, $request->pickup_lng);
        $dropoff_point = new Point($request->dropoff_lat, $request->dropoff_lng);
        $zone = Zone::query()->whereContains('coordinates', $pickup_point)->whereContains('coordinates', $dropoff_point)->latest()->first();

        if (!$zone) {
            $errors = [];
            array_push($errors, ['code' => 'coordinates', 'message' => translate('messages.out_of_coverage')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        if (!$ride_category = RideCategory::find($request->ride_category_id)) {
            $errors = [];
            array_push($errors, ['code' => 'ride_category_id', 'message' => translate('messages.not_found')]);
            return response()->json([
                'errors' => $errors
            ], 404);
        }

        $tax = BusinessSetting::where('key', 'ride_sharing_tax')->first();
        $tax = $tax ? $tax->value : 0;
        try {
            $ride_request = new RideRequest();
            $ride_request->user_id = $request->user()->id;
            if ($delivery_man != null) $ride_request->rider()->associate($delivery_man);
            $ride_request->ride_category_id = $request->ride_category_id;
            $ride_request->zone_id = $zone->id;
            $ride_request->ride_status = $request->ride_status;
            $ride_request->ride_status = 'pending';
            $ride_request->pickup_point = $pickup_point;
            $ride_request->pickup_address = $request->pickup_address;
            $ride_request->dropoff_point = $dropoff_point;
            $ride_request->dropoff_address = $request->dropoff_address;
            $ride_request->estimated_time = $request->estimated_time;
            $ride_request->estimated_fare = round($ride_category->base_fare + ($ride_category->per_km_fare * $request->estimated_distance), config('round_up_to_digit'));
            $ride_request->estimated_distance = $request->estimated_distance;
            $ride_request->tax = $tax;
            $ride_request->total_fare = $ride_request->estimated_fare + 0;
            $ride_request->otp = rand(1000, 9999);

            // Calculate credits for customer and driver (no merchant for ride requests)
            $moduleId = $request->header('moduleId');

            $customerCredits = $this->calculateRideCreditsForUserType(
                userType: 'customer',
                fare: $ride_request->total_fare,
                distance: $ride_request->estimated_distance,
                moduleId: $moduleId
            );
            $driverCredits = $this->calculateRideCreditsForUserType(
                userType: 'driver',
                fare: $ride_request->total_fare,
                distance: $ride_request->estimated_distance,
                moduleId: $moduleId
            );

            $ride_request->customer_credits_required = $customerCredits;
            $ride_request->driver_credits_required = $driverCredits;

            // Check if customer has enough credits and deduct immediately
            $customer = $request->user();
            if ($customerCredits > 0) {
                if ($customer->credit_balance < $customerCredits) {
                    return response()->json([
                        'errors' => [
                            ['code' => 'insufficient_credits', 'message' => translate('messages.insufficient_credit_balance_to_place_ride_request')]
                        ]
                    ], 403);
                }

                // Deduct customer credits immediately
                $walletService = app(\App\Services\WalletService::class);
                $walletService->deductCredits(
                    user: $customer,
                    amount: $customerCredits,
                    transaction_type: 'usage',
                    reference: 'ride:pending',
                    details: 'Credits deducted for ride request placement'
                );

                $ride_request->customer_credits_status = 'deducted';
            } else {
                $ride_request->customer_credits_status = 'none';
            }

            // Set driver credit status to 'none' (will be deducted when driver accepts)
            $ride_request->driver_credits_status = 'none';

            $ride_request->save();
            return response()->json(['message' => translate('messages.ride_request_placed_successfully')], 200);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
        return response()->json([
            'errors' => [
                ['code' => 'failed', 'message' => translate('messages.failed_to_place_request')]
            ]
        ], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $ride_request_id)
    {
        if (!$ride_requests = RideRequest::with(['customer', 'rider', 'zone', 'vehicle_category'])->where('user_id', $request->user()->id)->find($ride_request_id)) return response()->json(['errors' => [['code' => 'ride_request_id', 'message' => translate('messages.ride_request_not_found')]]], 403);

        return response()->json(new RideRequestResource($ride_requests), 200);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RideRequest  $rideRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $ride_request_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:canceled',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $ride_request = RideRequest::where('user_id', $request->user()->id)->find($ride_request_id);

        if ($ride_request == null) {
            return response()->json(['errors' => [['code' => 'ride_request_id', 'message' => translate('messages.ride_request_not_found')]]], 403);
        }

        $ride_request->ride_status = $request->status;
        $ride_request->save();

        // Refund credits to customer and driver if applicable when canceling
        if ($request->status === 'canceled') {
            $creditRefundService = app(\App\Services\CreditRefundService::class);
            $creditRefundService->refundRideCredits($ride_request);
        }

        return response()->json(['message' => translate('messages.ride_request_canceled_successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RideRequest  $rideRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(RideRequest $rideRequest)
    {
        //
    }

    public function submit_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ride_request_id' => 'required',
            'delivery_man_id' => 'required',
            'comment' => 'required',
            'rating' => 'required|numeric|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (!$ride_request = RideRequest::find($request->ride_request_id)) {
            return response()->json(['errors' => [['code' => 'ride_request_id', 'message' => translate('messages.ride_request_not_found')]]], 403);
        }
        $multi_review = DMReview::where(['delivery_man_id' => $request->delivery_man_id, 'user_id' => $request->user()->id, 'ride_request_id' => $request->ride_request_id])->first();
        if (isset($multi_review)) {
            return response()->json([
                'errors' => [
                    ['code' => 'review', 'message' => translate('messages.already_submitted')]
                ]
            ], 403);
        }


        $image_array = [];
        if (!empty($request->file('attachment'))) {
            foreach ($request->file('attachment') as $image) {
                if ($image != null) {
                    if (!Storage::disk('public')->exists('review')) {
                        Storage::disk('public')->makeDirectory('review');
                    }
                    array_push($image_array, Storage::disk('public')->put('review', $image));
                }
            }
        }

        $review = new DMReview();
        $review->user_id = $request->user()->id;
        $review->delivery_man_id = $request->delivery_man_id;
        $review->ride_request_id = $request->ride_request_id;
        $review->comment = $request->comment;
        $review->rating = $request->rating;
        $review->attachment = json_encode($image_array);
        $review->save();

        return response()->json(['message' => translate('messages.review_submited_successfully')], 200);
    }

    /**
     * Calculate credits required for a ride request
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculate_credits(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric|min:0',
            'distance' => 'required|numeric|min:0',
            'user_type' => 'nullable|in:customer,driver',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $userType = $request->input('user_type', 'customer');
            $estimatedFare = $request->input('price');
            $estimatedDistance = $request->input('distance');
            $moduleId = $request->header('moduleId');

            $module = null;

            if ($moduleId != null) {
                $module = Module::find($moduleId);
            }

            $rule = null;

            if ($module != null && $module->module_type == 'ride_sharing') {
                // Try to find matching rule based on price range
                $rule = CreditDeductionRule::where('user_type', $userType)
                    ->where('condition_type', 'ride_hailing')
                    ->where('status', true)
                    ->when($moduleId, function ($query, $moduleId) {
                        $query->where(function ($q) use ($moduleId) {
                            $q->where('module_id', $moduleId)->orWhereNull('module_id');
                        });
                    })
                    ->orderBy('credits_to_deduct', 'DESC')
                    ->first();
            }

            if (!$rule) {
                // Try to find matching rule based on price range
                $rule = CreditDeductionRule::where('user_type', $userType)
                    ->where('condition_type', 'price_range')
                    ->where('status', true)
                    ->when($moduleId, function ($query, $moduleId) {
                        $query->where(function ($q) use ($moduleId) {
                            $q->where('module_id', $moduleId)->orWhereNull('module_id');
                        });
                    })
                    ->where(function ($query) use ($estimatedFare) {
                        $query->where('min_value', '<=', $estimatedFare)
                            ->where(function ($q) use ($estimatedFare) {
                                $q->where('max_value', '>=', $estimatedFare)
                                    ->orWhereNull('max_value');
                            });
                    })
                    ->orderBy('credits_to_deduct', 'DESC')
                    ->first();
            }


            // If no price range rule found, try distance range
            if (!$rule) {
                $rule = CreditDeductionRule::where('user_type', $userType)
                    ->where('condition_type', 'distance_range')
                    ->where('status', true)
                    ->when($moduleId, function ($query, $moduleId) {
                        $query->where(function ($q) use ($moduleId) {
                            $q->where('module_id', $moduleId)->orWhereNull('module_id');
                        });
                    })
                    ->where(function ($query) use ($estimatedDistance) {
                        $query->where('min_value', '<=', $estimatedDistance)
                            ->where(function ($q) use ($estimatedDistance) {
                                $q->where('max_value', '>=', $estimatedDistance)
                                    ->orWhereNull('max_value');
                            });
                    })
                    ->orderBy('credits_to_deduct', 'DESC')
                    ->first();
            }

            // Calculate credits based on rule
            $creditsRequired = 0;
            $ruleName = null;
            $ruleCondition = null;

            if ($rule) {
                $creditsRequired = $rule->credits_to_deduct;
                $ruleName = $rule->name;
                $ruleCondition = $rule->condition_type;
            }

            return response()->json([
                'credits_required' => (float) $creditsRequired,
                'rule_applied' => [
                    'name' => $ruleName,
                    'condition_type' => $ruleCondition,
                    'rule_id' => $rule ? $rule->id : null
                ],
                'calculation_details' => [
                    'estimated_fare' => (float) $estimatedFare,
                    'estimated_distance' => (float) $estimatedDistance,
                    'user_type' => $userType,
                    'module_id' => (int) $moduleId
                ]
            ], 200);
        } catch (\Exception $e) {
            info($e);
            return response()->json([
                'errors' => [
                    ['code' => 'calculation_error', 'message' => 'Failed to calculate credits. Please try again.']
                ]
            ], 500);
        }
    }

    /**
     * Helper method to calculate credits for a specific user type for ride requests
     *
     * @param string $userType
     * @param float $fare
     * @param float $distance
     * @return float
     */
    private function calculateRideCreditsForUserType(
        string $userType,
        float $fare,
        float $distance = 0,
        ?int $moduleId = null
    ): float {
        // Build base query
        $query = CreditDeductionRule::where('user_type', $userType)
            ->where('status', true);

        // Add module filter if provided
        $module = null;

        if ($moduleId) {
            $query->where(function ($q) use ($moduleId) {
                $q->where('module_id', $moduleId)
                    ->orWhereNull('module_id');
            });

            $module = Module::find($moduleId);
        }

        $rule = null;

        if ($module != null && $module->module_type == 'ride_sharing') {
            // Try to find matching rule based on price range
            $rule = (clone $query)
                ->where('condition_type', 'ride_hailing')
                ->orderBy('credits_to_deduct', 'DESC')
                ->first();
        }

        info('Ride hailing rule for ' . $userType, ['is_found' => $rule != null]);

        if (!$rule) {
            // Try to find matching rule based on price range
            $rule = (clone $query)
                ->where('condition_type', 'price_range')
                ->where(function ($query) use ($fare) {
                    $query->where('min_value', '<=', $fare)
                        ->where(function ($q) use ($fare) {
                            $q->where('max_value', '>=', $fare)
                                ->orWhereNull('max_value');
                        });
                })
                ->orderBy('credits_to_deduct', 'DESC')
                ->first();
        }

        info('Price range rule for ' . $userType, ['is_found' => $rule != null]);

        // If no price range rule found, try distance range for delivery/parcel orders
        if (!$rule) {
            $rule = (clone $query)
                ->where('condition_type', 'distance_range')
                ->where(function ($query) use ($distance) {
                    $query->where('min_value', '<=', $distance)
                        ->where(function ($q) use ($distance) {
                            $q->where('max_value', '>=', $distance)
                                ->orWhereNull('max_value');
                        });
                })
                ->orderBy('credits_to_deduct', 'DESC')
                ->first();
        }

        info('Distance range rule for ' . $userType, ['is_found' => $rule != null]);

        // Return the credits to deduct or 0 if no rule found
        return $rule ? $rule->credits_to_deduct : 0; //$this->calculate_order_credits($orderAmount);
    }
}
