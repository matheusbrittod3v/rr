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
        Schema::table('tools', function (Blueprint $table) {
            // remove os campos antigos
            $table->dropColumn(['photo', 'patch']);

            // adiciona o novo campo único
            $table->string('photo_patch')->nullable()->after('descricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            // remove o campo unificado
            $table->dropColumn('photo_patch');

            // recria os campos antigos
            $table->string('photo')->nullable()->after('descricao');
            $table->string('patch')->nullable()->after('photo');
        });
    }
};
