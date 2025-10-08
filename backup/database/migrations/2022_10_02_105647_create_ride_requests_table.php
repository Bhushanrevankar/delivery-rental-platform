<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRideRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ride_requests', function (Blueprint $table) {
            $table->id()->from(100000);
            $table->foreignId('user_id');
            $table->foreignId('delivery_man_id')->nullable();
            $table->foreignId('ride_category_id');
            $table->foreignId('ride_zone_id');
            $table->string('ride_status');
            $table->point('pickup_point');
            $table->string('pickup_address')->nullable();
            $table->point('dropoff_point');
            $table->string('dropoff_address');
            $table->float('estimated_time');
            $table->decimal('estimated_fare', 23, 3);
            $table->float('actual_time')->nullable();
            $table->decimal('actual_fare', 23, 3)->nullable();
            $table->float('estimated_distance');
            $table->float('actual_distance')->nullable();
            $table->point('actual_pickup_point')->nullable();
            $table->point('actual_dropoff_point')->nullable();
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('dropoff_time')->nullable();
            $table->decimal('total_fare', 23, 3)->nullable();
            $table->decimal('tax', 23, 3)->nullable();
            $table->integer('otp');
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
        Schema::dropIfExists('ride_requests');
    }
}
