<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->string('link');
            $table->integer('quantity');
            $table->decimal('charge', 10, 4); // Montant débité du solde client
            $table->integer('start_count')->nullable(); // Nb d'abonnés avant commande
            $table->integer('remains')->nullable();     // Reste à livrer
            $table->enum('status', [
                'pending',
                'processing',
                'in_progress',
                'completed',
                'partial',
                'cancelled',
                'failed',
                'refunded'
            ])->default('pending');
            $table->string('provider_order_id')->nullable(); // ID retourné par Peakerr/JAP
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('refill_requested')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('provider_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
