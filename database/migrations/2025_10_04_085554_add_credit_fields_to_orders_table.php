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
        Schema::table('orders', function (Blueprint $table) {
            // Credit requirement fields
            $table->decimal('customer_credits_required', 10, 2)->default(0)->after('order_amount');
            $table->decimal('driver_credits_required', 10, 2)->default(0)->after('customer_credits_required');
            $table->decimal('merchant_credits_required', 10, 2)->default(0)->after('driver_credits_required');

            // Credit status fields - possible values: 'none', 'pending', 'deducted', 'refunded'
            $table->enum('customer_credits_status', ['none', 'pending', 'deducted', 'refunded'])->default('none')->after('merchant_credits_required');
            $table->enum('driver_credits_status', ['none', 'pending', 'deducted', 'refunded'])->default('none')->after('customer_credits_status');
            $table->enum('merchant_credits_status', ['none', 'pending', 'deducted', 'refunded'])->default('none')->after('driver_credits_status');

            // Add index for driver credit filtering
            $table->index('driver_credits_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['driver_credits_required']);
            $table->dropColumn([
                'customer_credits_required',
                'driver_credits_required',
                'merchant_credits_required',
                'customer_credits_status',
                'driver_credits_status',
                'merchant_credits_status',
            ]);
        });
    }
};
