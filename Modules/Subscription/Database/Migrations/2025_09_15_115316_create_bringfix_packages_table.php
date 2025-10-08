<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBringfixPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bringfix_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('trips_per_month');
            $table->decimal('max_distance_km', 8, 2);
            $table->enum('trip_type', ['one_way', 'two_way']);
            $table->decimal('price', 8, 2);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('bringfix_packages');
    }
}
