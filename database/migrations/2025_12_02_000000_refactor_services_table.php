<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For PostgreSQL, we need to drop dependent tables first or use CASCADE
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TABLE IF EXISTS service_custom_fields, service_requirements, services CASCADE');
        } else {
            // Drop dependent tables first for other databases
            Schema::dropIfExists('service_custom_fields');
            Schema::dropIfExists('service_requirements');
            Schema::dropIfExists('services');
        }
        
        // Create the new services table with the required fields
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marca_id');
            $table->string('photo_patch')->nullable();
            $table->string('nome_servico');
            $table->text('descricao')->nullable();
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('marca_id')->references('id')->on('marcas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // For PostgreSQL, we need to drop tables with CASCADE
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP TABLE IF EXISTS services CASCADE');
        } else {
            // Drop the new services table
            Schema::dropIfExists('services');
        }
        
        // Recreate the old services table structure
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_group_id');
            $table->string('service_id');
            $table->string('service_type');
            $table->string('service_name');
            $table->string('qnt')->default('0');
            $table->string('server')->default('0');
            $table->string('min_qnt')->default('0');
            $table->string('max_qnt')->default('0');
            $table->string('credit');
            $table->string('time');
            $table->text('info')->nullable();
            $table->timestamps();
            
            $table->foreign('service_group_id')->references('id')->on('service_groups')->onDelete('cascade');
        });
    }
};