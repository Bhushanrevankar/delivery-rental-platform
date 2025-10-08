<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Change all credit-related fields from integer to decimal(10, 2)
     * to support fractional credit values.
     */
    public function up(): void
    {
        // Update subscription_packages table
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->decimal('credits', 10, 2)->change();
        });

        // Update subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('total_credits', 10, 2)->change();
            $table->decimal('remaining_credits', 10, 2)->change();
        });

        // Update credit_transactions table
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });

        // Update credit_deduction_rules table - expand from decimal(8,2) to decimal(10,2)
        Schema::table('credit_deduction_rules', function (Blueprint $table) {
            $table->decimal('credits_to_deduct', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Rollback changes by converting decimal fields back to integer.
     * Note: This will truncate any fractional values.
     */
    public function down(): void
    {
        // Revert subscription_packages table
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->integer('credits')->change();
        });

        // Revert subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('total_credits')->change();
            $table->integer('remaining_credits')->change();
        });

        // Revert credit_transactions table
        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->integer('amount')->change();
        });

        // Revert credit_deduction_rules table back to decimal(8,2)
        Schema::table('credit_deduction_rules', function (Blueprint $table) {
            $table->decimal('credits_to_deduct', 8, 2)->change();
        });
    }
};
