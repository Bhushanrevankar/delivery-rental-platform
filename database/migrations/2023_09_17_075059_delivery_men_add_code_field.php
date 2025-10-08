<?php

use App\Models\DeliveryMan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RandomLib\Factory;

class DeliveryMenAddCodeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->string('code');
        });

        // generate codes for all existing delivery men.
        $randomFactory = new Factory;
        $mediumGenerator = $randomFactory->getMediumStrengthGenerator();
        $characters = '0123456789';

        $deliveryMen = DeliveryMan::all();
        DB::beginTransaction();
        foreach ($deliveryMen as $deliveryMan) {
            $code = $mediumGenerator->generateString(6, $characters);
            while (DeliveryMan::where('code', $code)->exists()) {
                $code = $mediumGenerator->generateString(6, $characters);
            }
            $deliveryMan->code = $code;
            $deliveryMan->save();
        }
        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
}
