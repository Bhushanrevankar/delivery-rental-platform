<?php

use App\Models\DeliveryMan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeliveryMenZonesManyToManyDropForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->dropColumn(['zone_id', 'ride_zone_id']);
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
            $table->bigInteger('zone_id')->unsigned()->nullable()->after('vehicle_owner_noc');
            $table->bigInteger('ride_zone_id')->unsigned()->nullable()->after('ride_category_id');
        });

        $deliveryMen = DeliveryMan::all();
        // NOTE: This loop uses ORM Eloquent API, to before rollback, delete change the model file yet.
        foreach ($deliveryMen as $deliveryMan) {
            $zone = $deliveryMan->zones()->first();
            if ($zone != null) {
                $deliveryMan->zone_id = $zone->id;
            }

            $rideZone = $deliveryMan->ride_zones()->first();
            if ($rideZone != null) {
                $deliveryMan->ride_zone_id = $rideZone->id;
            }

            $deliveryMan->save();
        }
    }
}
