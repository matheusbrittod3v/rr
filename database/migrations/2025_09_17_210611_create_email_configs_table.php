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
        Schema::create('email_configs', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->integer('port');
            $table->string('encryption_type'); // Tipo de criptografia
            $table->string('email');
            $table->string('password');
            $table->string('type')->default('smtp'); // smtp por padrão
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id'); // Apenas uma configuração por usuário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_configs');
    }
};
