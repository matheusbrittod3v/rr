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
        Schema::create('service_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('custom_name')->nullable();
            $table->text('custom_info')->nullable();
            $table->string('custom_len')->nullable();
            $table->string('max_length')->nullable();
            $table->string('regex')->nullable();
            $table->string('is_alpha')->nullable();
            $table->timestamps();
            
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_custom_fields');
    }
};