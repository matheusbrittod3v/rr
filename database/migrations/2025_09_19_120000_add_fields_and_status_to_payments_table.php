<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('number_whatsapp')->nullable()->after('tool_id');
            $table->string('email')->nullable()->after('number_whatsapp');
            
            // For PostgreSQL, we need to use raw SQL to properly change the column type
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE payments ALTER COLUMN status TYPE VARCHAR(255)");
                DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'nao pago'");
                DB::statement("ALTER TABLE payments ADD CONSTRAINT payments_status_check CHECK (status IN ('nao pago', 'pago', 'refund'))");
            } else {
                // For other databases (MySQL, SQLite)
                $table->enum('status', ['nao pago', 'pago', 'refund'])->default('nao pago')->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['number_whatsapp', 'email']);
            
            // For PostgreSQL, we need to drop the constraint first
            if (DB::getDriverName() === 'pgsql') {
                DB::statement("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_status_check");
                DB::statement("ALTER TABLE payments ALTER COLUMN status TYPE VARCHAR(255)");
                DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'nao pago'");
            } else {
                // For other databases (MySQL, SQLite)
                $table->enum('status', ['nao pago', 'pago'])->default('nao pago')->change();
            }
        });
    }
};