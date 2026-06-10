<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('method', [
                'mtn_mobile_money',
                'moov_money',
                'wave',
                'orange_money',
                'crypto_usdt',
                'crypto_btc',
                'paypal',
                'card'
            ]);
            $table->string('transaction_ref')->unique()->nullable();
            $table->jsonb('metadata')->nullable(); // Données brutes du callback paiement
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
