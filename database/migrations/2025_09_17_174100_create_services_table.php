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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};