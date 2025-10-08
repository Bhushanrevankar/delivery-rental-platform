<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id');
            $table->morphs('subscriber'); // This will create subscriber_id and subscriber_type
            $table->dateTime('expiry_date');
            $table->integer('total_credits');
            $table->integer('remaining_credits');
            $table->boolean('status')->default(true);
            $table->boolean('is_trial')->default(false);
            $table->tinyInteger('total_package_renewed')->default(0);
            $table->dateTime('renewed_at')->nullable();
            $table->boolean('is_canceled')->default(false);
            $table->enum('canceled_by', ['none', 'admin', 'user'])->default('none');
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
        Schema::dropIfExists('subscriptions');
    }
}
