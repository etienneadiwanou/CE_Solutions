<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->enum('type', [
                'deposit',    // Rechargement de solde
                'order',      // Débit pour commande
                'refund',     // Remboursement commande
                'bonus',      // Bonus parrainage
                'adjustment'  // Ajustement admin
            ]);
            $table->decimal('amount', 10, 4);
            $table->decimal('balance_before', 10, 4);
            $table->decimal('balance_after', 10, 4);
            $table->string('description')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable(); // 'orders', 'payments', etc.
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
