<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, make sure we have some marcas
        $marcas = DB::table('marcas')->pluck('id')->toArray();
        
        if (empty($marcas)) {
            // If no marcas exist, create some sample ones
            DB::table('marcas')->insert([
                ['nome' => 'Apple', 'caminho_imagem' => '/images/apple.png', 'created_at' => now(), 'updated_at' => now()],
                ['nome' => 'Samsung', 'caminho_imagem' => '/images/samsung.png', 'created_at' => now(), 'updated_at' => now()],
                ['nome' => 'Xiaomi', 'caminho_imagem' => '/images/xiaomi.png', 'created_at' => now(), 'updated_at' => now()],
            ]);
            
            $marcas = DB::table('marcas')->pluck('id')->toArray();
        }
        
        // Sample service data with the new structure
        $services = [
            [
                'marca_id' => $marcas[array_rand($marcas)],
                'photo_patch' => '/images/service1.jpg',
                'nome_servico' => 'Unlock Service',
                'descricao' => 'Professional device unlocking service for all carrier locks',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marca_id' => $marcas[array_rand($marcas)],
                'photo_patch' => '/images/service2.jpg',
                'nome_servico' => 'IMEI Check',
                'descricao' => 'Comprehensive IMEI verification and device history report',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'marca_id' => $marcas[array_rand($marcas)],
                'photo_patch' => '/images/service3.jpg',
                'nome_servico' => 'Firmware Update',
                'descricao' => 'Official firmware updates for your device',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Insert services and get their IDs
        $serviceIds = [];
        foreach ($services as $service) {
            $serviceIds[] = DB::table('services')->insertGetId($service);
        }
        
        // Sample custom fields data
        $customFields = [
            [
                'service_id' => $serviceIds[0],
                'parametros_campo' => json_encode([
                    'field_name' => 'imei',
                    'field_type' => 'text',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_id' => $serviceIds[1],
                'parametros_campo' => json_encode([
                    'field_name' => 'report_type',
                    'field_type' => 'text',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'service_id' => $serviceIds[2],
                'parametros_campo' => json_encode([
                    'field_name' => 'device_model',
                    'field_type' => 'text',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('service_custom_fields')->insert($customFields);
    }
}