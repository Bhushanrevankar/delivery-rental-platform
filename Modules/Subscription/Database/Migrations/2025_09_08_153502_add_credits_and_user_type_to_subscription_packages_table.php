<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreditsAndUserTypeToSubscriptionPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->string('user_type')->after('id')->comment('e.g., merchant, customer, driver');
            $table->integer('credits')->after('price');
            $table->string('plan_type')->default('standard')->after('user_type')->comment('e.g., standard, bringfix, bringfam, bringcorporate');
            $table->integer('trips')->nullable()->after('credits');
            $table->integer('members_included')->nullable()->after('trips');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn('user_type');
            $table->dropColumn('credits');
            $table->dropColumn('plan_type');
            $table->dropColumn('trips');
            $table->dropColumn('members_included');
        });
    }
}
