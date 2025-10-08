<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
            $table->dropColumn('reference');
            $table->nullableMorphs('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->integer('amount')->change();
            $table->dropMorphs('reference');
            $table->string('reference')->nullable()->comment('e.g., order_id, ride_id, subscription_id');
        });
    }
};
