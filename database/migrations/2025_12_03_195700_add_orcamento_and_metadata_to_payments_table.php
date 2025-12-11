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
            // Add orcamento_id if it doesn't exist
            if (!Schema::hasColumn('payments', 'orcamento_id')) {
                $table->foreignId('orcamento_id')->nullable()->constrained('orcamentos')->onDelete('cascade');
            }
            
            // Add metadata if it doesn't exist
            if (!Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'orcamento_id')) {
                $table->dropForeignIdFor(\App\Models\Orcamento::class);
                $table->dropColumn('orcamento_id');
            }
            
            if (Schema::hasColumn('payments', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
