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
        Schema::table('ride_requests', function (Blueprint $table) {
            // Credit requirement fields - no merchant credits for ride requests
            $table->decimal('customer_credits_required', 10, 2)->default(0)->after('total_fare');
            $table->decimal('driver_credits_required', 10, 2)->default(0)->after('customer_credits_required');

            // Credit status fields - possible values: 'none', 'pending', 'deducted', 'refunded'
            $table->enum('customer_credits_status', ['none', 'pending', 'deducted', 'refunded'])->default('none')->after('driver_credits_required');
            $table->enum('driver_credits_status', ['none', 'pending', 'deducted', 'refunded'])->default('none')->after('customer_credits_status');

            // Add index for driver credit filtering
            $table->index('driver_credits_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropIndex(['driver_credits_required']);
            $table->dropColumn([
                'customer_credits_required',
                'driver_credits_required',
                'customer_credits_status',
                'driver_credits_status',
            ]);
        });
    }
};
