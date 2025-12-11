<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsApi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WhatsAppApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('whats_apis')->truncate();
        
        // Get the first user or create one if none exists
        $user = User::first() ?? User::factory()->create();
        
        // Create the default WhatsApp API configuration
        WhatsApi::create([
            'name' => 'Default WhatsApp API',
            'host' => 'http://wppapi-api-pce6bu-9412e1-31-97-14-4.traefik.me',
            'key' => 'asdfghjksjhggbnmfdfghjhgf',
            'type' => 'string',
            'authenticate' => 'x-api-key',
            'instance_name' => null,
            'numero_instancia' => null,
            'connection_status' => 'disconnected', // disconnected, connected
            'user_id' => $user->id,
        ]);
    }
}