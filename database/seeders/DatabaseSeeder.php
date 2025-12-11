<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Executar os seeders
        $this->call([
            GatewayPagamentoSeeder::class,
            ServiceSeeder::class,
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,
            ProviderQueueTestSeeder::class,
        ]);
    }
}