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
        Schema::table('payments', function (Blueprint $table) {
            // Drop foreign key first if it exists, then drop the column
            try {
                // Most DB drivers accept dropForeign with array
                $table->dropForeign(['tool_id']);
            } catch (\Exception $e) {
                // ignore if constraint not found
            }

            if (Schema::hasColumn('payments', 'tool_id')) {
                $table->dropColumn('tool_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'tool_id')) {
                $table->unsignedBigInteger('tool_id')->nullable()->after('gateway_id');
                $table->foreign('tool_id')->references('id')->on('tools')->onDelete('cascade');
            }
        });
    }
};
