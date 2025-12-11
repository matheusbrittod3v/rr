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
        Schema::create('service_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('type')->nullable();
            $table->string('field_name');
            $table->string('field_type');
            $table->text('description')->nullable();
            $table->text('field_options')->nullable();
            $table->string('regex')->nullable();
            $table->string('admin_only')->nullable();
            $table->boolean('required')->default(false);
            $table->timestamps();
            
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requirements');
    }
};