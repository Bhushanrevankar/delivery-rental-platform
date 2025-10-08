<?php

use App\Models\RideCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterParcelCategoriesAddVehicleTypeAndCharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcel_categories', function (Blueprint $table) {
            $table->dropIndex('parcel_categories_name_unique');
            $table->after('module_id', function ($t) {
                $t->double('per_km_shipping_charge', 16, 3)->default(12);
                $t->double('minimum_shipping_charge', 16, 3)->default(70);
                $t->double('commission_dm', 16, 3)->default(90);
                $t->foreignIdFor(RideCategory::class)
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcel_categories', function (Blueprint $table) {
            $table->unique('name');
            $table->dropForeign('parcel_categories_ride_category_id_foreign');
            $table->dropColumn(['per_km_shipping_charge', 'minimum_shipping_charge', 'commission_dm', 'ride_category_id']);
        });
    }
}
