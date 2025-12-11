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
        Schema::create('fila_orcamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orcamento_id');
            $table->unsignedBigInteger('prestador_id')->nullable();
            $table->timestamps();
            
            $table->foreign('orcamento_id')->references('id')->on('orcamentos')->onDelete('cascade');
            $table->foreign('prestador_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fila_orcamentos');
    }
};