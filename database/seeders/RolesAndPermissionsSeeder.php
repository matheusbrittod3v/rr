<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Global permission to view all data
            ['name' => 'view_all_data', 'description' => 'Can view data from all users'],
            
            // Dashboard permission
            ['name' => 'view_dashboard', 'description' => 'Can view the dashboard'],
            
            // Service permissions
            ['name' => 'view_services', 'description' => 'Can view services'],
            ['name' => 'create_services', 'description' => 'Can create services'],
            ['name' => 'edit_services', 'description' => 'Can edit services'],
            ['name' => 'delete_services', 'description' => 'Can delete services'],
            
            // User permissions
            ['name' => 'view_users', 'description' => 'Can view users'],
            ['name' => 'create_users', 'description' => 'Can create users'],
            ['name' => 'edit_users', 'description' => 'Can edit users'],
            ['name' => 'delete_users', 'description' => 'Can delete users'],
            
            // Payment permissions
            ['name' => 'view_payments', 'description' => 'Can view payments'],
            ['name' => 'create_payments', 'description' => 'Can create payments'],
            ['name' => 'edit_payments', 'description' => 'Can edit payments'],
            ['name' => 'delete_payments', 'description' => 'Can delete payments'],
            
            // Brand/Marca permissions
            ['name' => 'view_marcas', 'description' => 'Can view marcas'],
            ['name' => 'create_marcas', 'description' => 'Can create marcas'],
            ['name' => 'edit_marcas', 'description' => 'Can edit marcas'],
            ['name' => 'delete_marcas', 'description' => 'Can delete marcas'],
            
            // WhatsApp API permissions
            ['name' => 'view_whatsapis', 'description' => 'Can view WhatsApp APIs'],
            ['name' => 'create_whatsapis', 'description' => 'Can create WhatsApp APIs'],
            ['name' => 'edit_whatsapis', 'description' => 'Can edit WhatsApp APIs'],
            ['name' => 'delete_whatsapis', 'description' => 'Can delete WhatsApp APIs'],
            
            // Email Config permissions
            ['name' => 'view_emailconfigs', 'description' => 'Can view email configurations'],
            ['name' => 'create_emailconfigs', 'description' => 'Can create email configurations'],
            ['name' => 'edit_emailconfigs', 'description' => 'Can edit email configurations'],
            ['name' => 'delete_emailconfigs', 'description' => 'Can delete email configurations'],
            
            // Gateway Pagamento permissions
            ['name' => 'view_gatewaypagamentos', 'description' => 'Can view payment gateways'],
            ['name' => 'create_gatewaypagamentos', 'description' => 'Can create payment gateways'],
            ['name' => 'edit_gatewaypagamentos', 'description' => 'Can edit payment gateways'],
            ['name' => 'delete_gatewaypagamentos', 'description' => 'Can delete payment gateways'],
            
            // Report permissions
            ['name' => 'view_reports', 'description' => 'Can view reports'],
            ['name' => 'generate_reports', 'description' => 'Can generate reports'],
            
            // Role/Permission management
            ['name' => 'manage_roles', 'description' => 'Can manage roles'],
            ['name' => 'manage_permissions', 'description' => 'Can manage permissions'],
        ];
        
        // Insert permissions
        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }
        
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full access']
        );
        
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'Manager with moderate access']
        );
        
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['description' => 'Regular user with limited access']
        );
        
        // Assign all permissions to admin role
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));
        
        // Assign specific permissions to manager role
        $managerPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_all_data',
            'view_services',
            'edit_services',
            'view_users',
            'view_payments',
            'view_marcas',
            'edit_marcas',
            'view_whatsapis',
            'edit_whatsapis',
            'view_emailconfigs',
            'edit_emailconfigs',
            'view_gatewaypagamentos',
            'edit_gatewaypagamentos',
            'view_reports',
            'generate_reports'
        ])->get();
        
        $managerRole->permissions()->sync($managerPermissions->pluck('id'));
        
        // Assign basic permissions to user role
        $userPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_services',
            'view_payments',
            'view_whatsapis',
            'view_emailconfigs',
            'view_gatewaypagamentos'
        ])->get();
        
        $userRole->permissions()->sync($userPermissions->pluck('id'));
    }
}