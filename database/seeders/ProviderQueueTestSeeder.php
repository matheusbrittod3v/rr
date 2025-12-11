<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Orcamento;

class ProviderQueueTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test providers
        $provider1 = User::create([
            'name' => 'Provider One',
            'email' => 'provider1@example.com',
            'numero' => '123456789',
            'is_provider' => true,
            'password' => bcrypt('password'),
        ]);

        $provider2 = User::create([
            'name' => 'Provider Two',
            'email' => 'provider2@example.com',
            'numero' => '987654321',
            'is_provider' => true,
            'password' => bcrypt('password'),
        ]);

        $nonProvider = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'numero' => '456789123',
            'is_provider' => false,
            'password' => bcrypt('password'),
        ]);

        // Create test budgets
        $budget1 = Orcamento::create([
            'email' => 'client1@example.com',
            'numero' => 'ORC001',
            'informacoes_adicionais' => json_encode(['service' => 'Service 1']),
            'valor' => 100.00,
            'aceito' => 'nao'
        ]);

        $budget2 = Orcamento::create([
            'email' => 'client2@example.com',
            'numero' => 'ORC002',
            'informacoes_adicionais' => json_encode(['service' => 'Service 2']),
            'valor' => 200.00,
            'aceito' => 'nao'
        ]);
    }
}