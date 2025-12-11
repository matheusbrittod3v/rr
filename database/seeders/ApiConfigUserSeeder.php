<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApiConfig;
use App\Models\User;

class ApiConfigUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all existing API configs that don't have a user_id
        $apiConfigs = ApiConfig::whereNull('user_id')->get();
        
        // Get the first user or create one if none exist
        $user = User::first() ?? User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Assign all existing API configs to the first user
        foreach ($apiConfigs as $apiConfig) {
            $apiConfig->update(['user_id' => $user->id]);
        }
        
        // Create a sample API config for the user if none exist
        if ($user->apiConfigs()->count() == 0) {
            ApiConfig::create([
                'user_id' => $user->id,
                'key' => 'sample_api_key',
                'host' => 'https://api.example.com',
                'value' => 'sample_api_value',
                'description' => 'Sample API configuration',
                'is_active' => true,
            ]);
        }
    }
}
