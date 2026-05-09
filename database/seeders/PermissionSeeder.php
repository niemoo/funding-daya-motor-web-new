<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.export',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Attendances
            'attendances.view',
            'attendances.view_all',
            'attendances.edit',
            'attendances.delete',
            'attendances.export',
            'attendances.invoice',

            // Parts
            'parts.view',
            'parts.create',
            'parts.edit',
            'parts.delete',
            'parts.import',

            // Part Groups
            'part-groups.view',
            'part-groups.create',
            'part-groups.edit',
            'part-groups.delete',

            // Profile
            'profile.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign semua permission ke role Admin
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all());
        }

        // Assign permission dasar ke role Sales
        $sales = Role::where('name', 'Sales')->first();
        if ($sales) {
            $sales->syncPermissions([
                'dashboard.view',
                'attendances.view',
                'attendances.export',
                'attendances.invoice',
                'parts.view',
                'part-groups.view',
                'profile.view',
            ]);
        }
    }
}