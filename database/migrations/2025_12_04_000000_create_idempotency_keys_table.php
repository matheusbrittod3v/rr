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
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('operation_type')->index(); // e.g., 'whatsapp_notification', 'email_notification'
            $table->text('payload')->nullable(); // Store the payload for reference
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['key', 'operation_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};