<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRideCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_categories', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->float('base_fare', 16, 3);
            $table->float('per_km_fare', 16, 3);
            $table->float('per_min_waiting_fare', 16, 3);
            $table->boolean('status')->default(1);
            // $table->foreignId('zone_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ride_categories');
    }
}
