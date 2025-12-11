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
        // Drop the existing service_custom_fields table
        Schema::dropIfExists('service_custom_fields');
        
        // Create the new service_custom_fields table with the required fields
        Schema::create('service_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->json('parametros_campo')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new service_custom_fields table
        Schema::dropIfExists('service_custom_fields');
        
        // Recreate the old service_custom_fields table structure
        Schema::create('service_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('field_name');
            $table->string('field_type');
            $table->boolean('required')->default(false);
            $table->text('options')->nullable();
            $table->timestamps();
            
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }
};