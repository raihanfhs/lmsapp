<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // Good practice to import Permission too

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Pengelola']);
        Role::create(['name' => 'Teacher']);
        Role::create(['name' => 'Student']);

        // We could create permissions here later, e.g.:
        // Permission::create(['name' => 'edit articles']);
    }
}