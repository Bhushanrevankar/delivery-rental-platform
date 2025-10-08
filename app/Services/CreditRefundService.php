<?php

namespace App\Services;

use App\Models\Order;
use App\Models\RideRequest;
use App\Services\WalletService;

/**
 * Service for handling credit refunds when orders or rides are canceled
 */
class CreditRefundService
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Refund credits for a canceled order
     *
     * @param Order $order
     * @return array - Returns status of each refund (customer, driver, merchant)
     */
    public function refundOrderCredits(Order $order): array
    {
        $refunds = [
            'customer' => false,
            'driver' => false,
            'merchant' => false,
        ];

        // Refund customer credits if they were deducted
        if ($order->customer_credits_status === 'deducted' && $order->customer_credits_required > 0) {
            $customer = $order->customer;
            if ($customer) {
                $this->walletService->refundCredits(
                    user: $customer,
                    amount: $order->customer_credits_required,
                    transaction_type: 'refund',
                    reference: 'order:' . $order->id,
                    details: 'Credits refunded for canceled order #' . $order->id
                );
                $order->customer_credits_status = 'refunded';
                $refunds['customer'] = true;
            }
        }

        // Refund driver credits if they were deducted
        if ($order->driver_credits_status === 'deducted' && $order->driver_credits_required > 0) {
            $driver = $order->delivery_man;
            if ($driver) {
                $this->walletService->refundCredits(
                    user: $driver,
                    amount: $order->driver_credits_required,
                    transaction_type: 'refund',
                    reference: 'order:' . $order->id,
                    details: 'Credits refunded for canceled order #' . $order->id
                );
                $order->driver_credits_status = 'refunded';
                $refunds['driver'] = true;
            }
        }

        // Refund merchant credits if they were deducted
        if ($order->merchant_credits_status === 'deducted' && $order->merchant_credits_required > 0) {
            $merchant = $order->store?->vendor;
            if ($merchant) {
                $this->walletService->refundCredits(
                    user: $merchant,
                    amount: $order->merchant_credits_required,
                    transaction_type: 'refund',
                    reference: 'order:' . $order->id,
                    details: 'Credits refunded for canceled order #' . $order->id
                );
                $order->merchant_credits_status = 'refunded';
                $refunds['merchant'] = true;
            }
        }

        $order->save();

        return $refunds;
    }

    /**
     * Refund credits for a canceled ride request
     *
     * @param RideRequest $rideRequest
     * @return array - Returns status of each refund (customer, driver)
     */
    public function refundRideCredits(RideRequest $rideRequest): array
    {
        $refunds = [
            'customer' => false,
            'driver' => false,
        ];

        // Refund customer credits if they were deducted
        if ($rideRequest->customer_credits_status === 'deducted' && $rideRequest->customer_credits_required > 0) {
            $customer = $rideRequest->customer;
            if ($customer) {
                $this->walletService->refundCredits(
                    user: $customer,
                    amount: $rideRequest->customer_credits_required,
                    transaction_type: 'refund',
                    reference: 'ride:' . $rideRequest->id,
                    details: 'Credits refunded for canceled ride request #' . $rideRequest->id
                );
                $rideRequest->customer_credits_status = 'refunded';
                $refunds['customer'] = true;
            }
        }

        // Refund driver credits if they were deducted
        if ($rideRequest->driver_credits_status === 'deducted' && $rideRequest->driver_credits_required > 0) {
            $driver = $rideRequest->rider;
            if ($driver) {
                $this->walletService->refundCredits(
                    user: $driver,
                    amount: $rideRequest->driver_credits_required,
                    transaction_type: 'refund',
                    reference: 'ride:' . $rideRequest->id,
                    details: 'Credits refunded for canceled ride request #' . $rideRequest->id
                );
                $rideRequest->driver_credits_status = 'refunded';
                $refunds['driver'] = true;
            }
        }

        $rideRequest->save();

        return $refunds;
    }

    /**
     * Check if an order is eligible for credit refunds
     *
     * @param Order $order
     * @return bool
     */
    public function isOrderEligibleForRefund(Order $order): bool
    {
        return in_array($order->order_status, ['canceled', 'failed', 'refunded']);
    }

    /**
     * Check if a ride request is eligible for credit refunds
     *
     * @param RideRequest $rideRequest
     * @return bool
     */
    public function isRideEligibleForRefund(RideRequest $rideRequest): bool
    {
        return in_array($rideRequest->ride_status, ['canceled', 'failed']);
    }
}
