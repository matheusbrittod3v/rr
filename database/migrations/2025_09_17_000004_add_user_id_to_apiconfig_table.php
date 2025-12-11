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
        Schema::table('apiconfig', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->unique(['user_id', 'key'], 'apiconfig_user_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apiconfig', function (Blueprint $table) {
            $table->dropUnique('apiconfig_user_key_unique');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};