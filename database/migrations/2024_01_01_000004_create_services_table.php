<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_per_1000', 10, 4); // Prix que tu affiches au client
            $table->integer('min_quantity')->default(10);
            $table->integer('max_quantity')->default(100000);
            $table->enum('type', ['default', 'drip_feed', 'subscriptions', 'package']);
            $table->foreignId('provider_id')->constrained()->restrictOnDelete();
            $table->string('provider_service_id'); // L'ID du service chez Peakerr/JAP
            $table->boolean('refill')->default(false);
            $table->boolean('cancel')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
