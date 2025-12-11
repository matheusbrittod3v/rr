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
        // Drop the service_requirements table (we're keeping service_custom_fields)
        Schema::dropIfExists('service_requirements');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the service_requirements table
        Schema::create('service_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('requirement_text');
            $table->timestamps();
            
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }
};