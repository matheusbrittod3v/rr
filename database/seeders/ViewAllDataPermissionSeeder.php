<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class ViewAllDataPermissionSeeder extends Seeder
{
    public function run()
    {
        // Cria a permissão
        $permission = Permission::firstOrCreate(
            ['name' => 'view_all_data'],
            ['description' => 'View all data regardless of ownership']
        );
        
        // Atribui ao Admin (se existir role admin)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}
