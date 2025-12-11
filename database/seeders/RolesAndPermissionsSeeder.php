<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

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

            // Chat Room permissions
            ['name' => 'view_chat_rooms', 'description' => 'Can view chat rooms'],
            ['name' => 'create_chat_rooms', 'description' => 'Can create chat rooms'],
            ['name' => 'edit_chat_rooms', 'description' => 'Can edit chat rooms'],
            ['name' => 'delete_chat_rooms', 'description' => 'Can delete chat rooms'],

            // Client permissions (mapped to ClientResource)
            ['name' => 'view_clients', 'description' => 'Can view clients'],
            ['name' => 'create_clients', 'description' => 'Can create clients'],
            ['name' => 'edit_clients', 'description' => 'Can edit clients'],
            ['name' => 'delete_clients', 'description' => 'Can delete clients'],

            // Orcamento permissions
            ['name' => 'view_orcamentos', 'description' => 'Can view budgets'],
            ['name' => 'create_orcamentos', 'description' => 'Can create budgets'],
            ['name' => 'edit_orcamentos', 'description' => 'Can edit budgets'],
            ['name' => 'delete_orcamentos', 'description' => 'Can delete budgets'],

            // Fila Orcamento permissions
            ['name' => 'view_fila_orcamentos', 'description' => 'Can view budget queue'],
            ['name' => 'create_fila_orcamentos', 'description' => 'Can create budget queue items'],
            ['name' => 'edit_fila_orcamentos', 'description' => 'Can edit budget queue items'],
            ['name' => 'delete_fila_orcamentos', 'description' => 'Can delete budget queue items'],

            // Fila Prestador permissions
            ['name' => 'view_fila_prestadores', 'description' => 'Can view provider queue'],
            ['name' => 'create_fila_prestadores', 'description' => 'Can create provider queue items'],
            ['name' => 'edit_fila_prestadores', 'description' => 'Can edit provider queue items'],
            ['name' => 'delete_fila_prestadores', 'description' => 'Can delete provider queue items'],

            // Admin Chat Access
            ['name' => 'access_admin_chat', 'description' => 'Can access any chat room as admin'],
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
            'generate_reports',
            'view_chat_rooms',
            'view_clients',
            'view_orcamentos',
            'edit_orcamentos',
            'view_fila_orcamentos',
            'view_fila_prestadores',
            'edit_fila_prestadores',
        ])->get();

        $managerRole->permissions()->sync($managerPermissions->pluck('id'));

        // Assign basic permissions to user role
        $userPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_payments',

        ])->get();

        $userRole->permissions()->sync($userPermissions->pluck('id'));
    }
}
