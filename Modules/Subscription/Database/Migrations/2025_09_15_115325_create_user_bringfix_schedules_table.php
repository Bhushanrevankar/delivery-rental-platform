<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBringfixSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bringfix_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_bringfix_subscription_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week');
            $table->time('time_slot');
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
        Schema::dropIfExists('user_bringfix_schedules');
    }
}
