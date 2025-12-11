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
        Schema::create('whats_apis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('host');
            $table->string('key');
            $table->string('type');
            $table->string('authenticate'); // Will store options like 'bearer', 'x-api-key', etc.
            $table->string('instance_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_apis');
    }
};
