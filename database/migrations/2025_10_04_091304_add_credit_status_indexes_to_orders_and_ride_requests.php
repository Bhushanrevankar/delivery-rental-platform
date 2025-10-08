<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to orders table for credit status fields
        Schema::table('orders', function (Blueprint $table) {
            // Composite indexes for refund queries
            $table->index(['customer_credits_status', 'order_status'], 'orders_customer_credit_status_order_status_index');
            $table->index(['driver_credits_status', 'order_status'], 'orders_driver_credit_status_order_status_index');
            $table->index(['merchant_credits_status', 'order_status'], 'orders_merchant_credit_status_order_status_index');
        });

        // Add indexes to ride_requests table for credit status fields
        Schema::table('ride_requests', function (Blueprint $table) {
            // Composite indexes for refund queries
            $table->index(['customer_credits_status', 'ride_status'], 'ride_requests_customer_credit_status_ride_status_index');
            $table->index(['driver_credits_status', 'ride_status'], 'ride_requests_driver_credit_status_ride_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_customer_credit_status_order_status_index');
            $table->dropIndex('orders_driver_credit_status_order_status_index');
            $table->dropIndex('orders_merchant_credit_status_order_status_index');
        });

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropIndex('ride_requests_customer_credit_status_ride_status_index');
            $table->dropIndex('ride_requests_driver_credit_status_ride_status_index');
        });
    }
};
