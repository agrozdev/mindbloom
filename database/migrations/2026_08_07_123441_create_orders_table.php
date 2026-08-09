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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('orderable');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('BGN');
            $table->string('status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->json('notify_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
