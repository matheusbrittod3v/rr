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
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->comment('Nome/identificação do banner');
            $table->json('imagens')->nullable()->comment('Array de caminhos das imagens');
            $table->integer('tempo_rotacao')->default(5000)->comment('Tempo de rotação em milissegundos');
            $table->boolean('ativo')->default(true)->comment('Se o banner está ativo');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
