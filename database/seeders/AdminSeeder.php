<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Spatie cache clear (safe)
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $users = [
            [
                'name'     => 'Super Administrator',
                'email'    => 'superadmin@admin.com',
                'password' => Hash::make('admin@1122'),
                'role'     => 'Super Administrator',
            ],
            [
                'name'     => 'Admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('admin@1122'),
                'role'     => 'Admin',
            ],
            [
                'name'     => 'Store Manager',
                'email'    => 'storemanager@admin.com',
                'password' => Hash::make('admin@1122'),
                'role'     => 'Store Manager',
            ],
            [
                'name'     => 'Accountant',
                'email'    => 'accountant@admin.com',
                'password' => Hash::make('admin@1122'),
                'role'     => 'Accountant',
            ],
            [
                'name'     => 'Branch Accountant',
                'email'    => 'branchaccountant@admin.com',
                'password' => Hash::make('admin@1122'),
                'role'     => 'Branch Accountant',
            ],
            [
                'name'     => 'Sales Associate',
                'email'    => 'sales@admin.com',
                'password' => Hash::make('admin@1122'),
                'role'     => 'Sales Associate',
            ],
        ];

        $guard = config('auth.defaults.guard', 'web');

        foreach ($users as $u) {
            $user = User::updateOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => $u['password']]
            );

            $role = Role::firstOrCreate(['name' => $u['role'], 'guard_name' => $guard]);
            $user->syncRoles([$role]);
        }

        // cache refresh
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
