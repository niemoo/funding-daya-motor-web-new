<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');
        $salesRoleId = DB::table('roles')->where('name', 'Sales')->value('id');

        DB::table('users')->insert([
            'role_id'    => $adminRoleId,
            'name'       => 'Super Admin',
            'email'      => 'admin@admin.com',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'role_id'    => $salesRoleId,
            'name'       => 'Sales User',
            'email'      => 'sales@sales.com',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'role_id'    => $salesRoleId,
            'name'       => 'Joko Nambo',
            'email'      => 'joko@sales.com',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
