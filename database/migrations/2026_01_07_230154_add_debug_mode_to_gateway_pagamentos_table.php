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
        Schema::table('gateway_pagamentos', function (Blueprint $table) {
            $table->boolean('debug_mode')->default(false)->after('token')->comment('Se ativo, aprova pagamentos automaticamente sem validar na gateway');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateway_pagamentos', function (Blueprint $table) {
            $table->dropColumn('debug_mode');
        });
    }
};
