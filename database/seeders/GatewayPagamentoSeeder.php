<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GatewayPagamento;

class GatewayPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GatewayPagamento::firstOrCreate([
            'name' => 'Mercado Pago',
            'url' => 'https://api.mercadopago.com/',
            'token' => null, // O token pode ser definido posteriormente
        ]);
    }
}