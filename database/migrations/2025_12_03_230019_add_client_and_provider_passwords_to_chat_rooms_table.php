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
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->string('client_password')->default('')->after('password');
            $table->string('provider_password')->default('')->after('client_password');
            $table->dropColumn('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropColumn(['client_password', 'provider_password']);
            $table->string('password')->after('room_code');
        });
    }
};
