<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVehicleColumnToDeliveryMenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->boolean('delivery');
            $table->boolean('ride_sharing')->default(0);
            $table->string('vehicle_reg_no')->nullable();
            $table->string('vehicle_rc')->nullable();
            $table->string('vehicle_owner_noc')->nullable();
            $table->foreignId('ride_zone_id')->nullable();
            $table->foreignId('ride_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->dropColumn('delivery');
            $table->dropColumn('ride_sharing');
            $table->dropColumn('vehicle_reg_no');
            $table->dropColumn('vehicle_rc');
            $table->dropColumn('vehicle_owner_noc');
            $table->dropColumn('ride_zone_id');
            $table->dropColumn('ride_category_id');
        });
    }
}
