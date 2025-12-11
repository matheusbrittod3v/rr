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
        Schema::create('chat_signals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained('chat_rooms')->onDelete('cascade');
            $table->string('sender_type'); // 'client', 'provider', 'admin'
            $table->string('type'); // 'offer', 'answer', 'candidate', 'hangup'
            $table->text('payload'); // JSON data
            $table->boolean('processed')->default(false); // To mark if the signal was already consumed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_signals');
    }
};
