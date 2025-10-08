<?php

use App\Models\WalletTransaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_requests', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->decimal('amount', 24, 3);
            $table->string('payment_channel');
            $table->string('account_name');
            $table->string('account_number');
            $table->text('proof_img_url');
            $table->string('status')
                ->default('pending')->comment('pending, approved, rejected, canceled');
            $table->timestamp('approved_at')->nullable();
            $table->foreignIdFor(WalletTransaction::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('deposit_requests');
    }
}
