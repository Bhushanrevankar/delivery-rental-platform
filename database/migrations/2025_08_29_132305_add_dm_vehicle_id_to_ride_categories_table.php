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
        Schema::table('ride_categories', function (Blueprint $table) {
            $table->foreignId('dm_vehicle_id')->nullable()->after('name')->constrained('d_m_vehicles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_categories', function (Blueprint $table) {
            $table->dropForeign(['dm_vehicle_id']);
            $table->dropColumn('dm_vehicle_id');
        });
    }
};
