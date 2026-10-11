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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            // No FK constraint: posts is MyISAM on some installs; Post deletes its comments itself.
            $table->foreignId('post_id')->index();
            $table->string('name');
            $table->string('email');
            $table->text('body');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('approved_at')->nullable()->index();
            $table->timestamp('approval_email_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
