<?php

use App\Models\DeliveryMan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DeliveryMenZonesManyToManyDataMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODO: error
        $deliveryMen = DeliveryMan::whereNotNull('zone_id')->has('zone')->get();
        $delivery_man_zone_data = $deliveryMen->map(function ($deliveryMan) {
            return [
                'delivery_man_id' => $deliveryMan->id,
                'zone_id' => $deliveryMan->zone_id
            ];
        });

        $deliveryMen = DeliveryMan::whereNotNull('ride_zone_id')->has('ride_zone')->get();
        $delivery_man_ride_zone_data = $deliveryMen->map(function ($deliveryMan) {
            return [
                'delivery_man_id' => $deliveryMan->id,
                'ride_zone_id' => $deliveryMan->ride_zone_id
            ];
        });

        DB::table('delivery_man_zone')->insert($delivery_man_zone_data->toArray());
        DB::table('delivery_man_ride_zone')->insert($delivery_man_ride_zone_data->toArray());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('delivery_man_zone')->truncate();
        DB::table('delivery_man_ride_zone')->truncate();
    }
}
