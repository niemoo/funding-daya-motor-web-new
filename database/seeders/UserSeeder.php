<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $salesRole = Role::where('name', 'Sales')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->syncRoles($adminRole);

        $sales1 = User::firstOrCreate(
            ['email' => 'sales@sales.com'],
            [
                'name'     => 'Sales User',
                'password' => Hash::make('password'),
            ]
        );
        $sales1->syncRoles($salesRole);

        $sales2 = User::firstOrCreate(
            ['email' => 'joko@sales.com'],
            [
                'name'     => 'Joko Nambo',
                'password' => Hash::make('password'),
            ]
        );
        $sales2->syncRoles($salesRole);
    }
}
