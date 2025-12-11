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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('nome');                 // Nome da ferramenta
            $table->decimal('price', 10, 2);        // Preço
            $table->text('descricao')->nullable();  // Descrição
            $table->string('photo')->nullable();    // Foto (URL ou path)
            $table->string('patch')->nullable();    // Patch
            $table->string('dhru_id')->nullable();  // DHru ID
            $table->boolean('is_active')->default(true); // Ativo/Inativo
            $table->integer('slot_qnt')->default(1);     // Quantidade de slots
            $table->integer('time')->default(0);         // Tempo (em minutos ou outro padrão)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
