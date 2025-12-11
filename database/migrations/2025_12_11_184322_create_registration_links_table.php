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
        Schema::create('registration_links', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->json('roles')->nullable(); // Array of role IDs
            $table->boolean('is_provider')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->timestamp('expires_at')->nullable();
            $table->integer('max_uses')->nullable(); // null = unlimited
            $table->integer('uses_count')->default(0);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_links');
    }
};
