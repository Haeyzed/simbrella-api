<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = config('acl.roles');
        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                [
                    'name' => $role['name'],
                    'display_name' => $role['display_name'] ?? null,
                    'description' => $role['description'] ?? null,
                    'guard_name' => 'api',
                ]
            );
        }

        // Create permissions
        $permissions = config('acl.permissions');
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'name' => $permission['name'],
                    'display_name' => $permission['display_name'] ?? null,
                    'description' => $permission['description'] ?? null,
                    'is_system' => $permission['is_system'] ?? false,
                    'guard_name' => 'api',
                ]
            );
        }

        // Assign all permissions to super admin role
        $superAdminRole = Role::where('name', config('acl.roles.sadmin.name'))->first();
        $superAdminRole->syncPermissions(Permission::all());

        // Assign specific permissions to admin role
        $adminRole = Role::where('name', config('acl.roles.admin.name'))->first();
        $adminRole->syncPermissions([
            'user_view',
            'user_create',
            'user_update',
            'user_delete',
            'user_restore',
            'role_view',
            'role_create',
            'role_update',
            'role_delete',
            'permission_view',
        ]);

        // Assign specific permissions to manager role
        $managerRole = Role::where('name', config('acl.roles.manager.name'))->first();
        $managerRole->syncPermissions([
            'user_view',
            'user_create',
            'user_update',
            'role_view',
        ]);

        // Assign basic permissions to user role
        $userRole = Role::where('name', config('acl.roles.user.name'))->first();
        $userRole->syncPermissions([
            'user_view',
        ]);
    }
}
