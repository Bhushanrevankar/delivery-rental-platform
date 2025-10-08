<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditDeductionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_deduction_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('user_type');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->enum('condition_type', ['distance_range', 'price_range', 'ride_hailing', 'ride_share']);
            $table->decimal('min_value', 8, 2)->nullable();
            $table->decimal('max_value', 8, 2)->nullable();
            $table->decimal('credits_to_deduct', 8, 2);
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
        Schema::dropIfExists('credit_deduction_rules');
    }
}
