<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRideRequestIdToDMReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('d_m_reviews', function (Blueprint $table) {
            $table->foreignId('ride_request_id')->nullable();
            $table->foreignId('order_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('d_m_reviews', function (Blueprint $table) {
            $table->dropColumn('ride_request_id');
            $table->foreignId('order_id')->change();
        });
    }
}
