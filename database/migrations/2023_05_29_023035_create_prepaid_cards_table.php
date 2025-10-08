<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrepaidCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prepaid_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_no')->unique();
            $table->decimal('price', 24, 3);
            $table->integer('duration_days');
            $table->string('pin');
            $table->boolean('is_used')->default(false);
            $table->nullableMorphs('usable');
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
        Schema::dropIfExists('prepaid_cards');
    }
}
