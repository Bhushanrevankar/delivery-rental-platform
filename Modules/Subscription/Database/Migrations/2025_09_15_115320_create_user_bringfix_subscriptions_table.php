<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBringfixSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bringfix_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bringfix_package_id')->constrained()->onDelete('cascade');
            $table->string('pickup_address');
            $table->string('pickup_lat');
            $table->string('pickup_lng');
            $table->string('dropoff_address');
            $table->string('dropoff_lat');
            $table->string('dropoff_lng');
            $table->decimal('route_distance_km', 8, 2);
            $table->integer('trips_total');
            $table->integer('trips_remaining');
            $table->timestamp('expiry_date');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
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
        Schema::dropIfExists('user_bringfix_subscriptions');
    }
}
