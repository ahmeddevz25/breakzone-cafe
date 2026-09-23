<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Spatie cache clear
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Guard (change if you use a custom one like 'admin')
        $guard = config('auth.defaults.guard', 'web');

        // --- Modules: base first, then children (singular action names) ---
        $modules = [
            [
                'base'     => 'store management',
                'children' => ['store add', 'store edit', 'store delete'],
            ],
            [
                'base'     => 'supplier management',
                'children' => ['supplier add', 'supplier edit', 'supplier delete'],
            ],
            [
                'base'     => 'brand management',
                'children' => ['brand add', 'brand edit', 'brand delete'],
            ],
            [
                'base'     => 'category management',
                'children' => ['category add', 'category edit', 'category delete'],
            ],
            [
                'base'     => 'unit management',
                'children' => ['unit add', 'unit edit', 'unit delete'],
            ],
            [
                'base'     => 'item management',
                'children' => ['item add', 'item edit', 'item delete'],
            ],
            [
                'base'     => 'ingredient management',
                'children' => ['ingredient add', 'ingredient edit', 'ingredient delete'],
            ],
            [
                'base'     => 'food management',
                'children' => ['food add', 'food edit', 'food delete'],
            ],
            [
                'base'     => 'purchase management',
                'children' => ['purchase add', 'purchase edit', 'purchase delete'],
            ],
            [
                'base'     => 'user management',
                'children' => ['user add', 'user edit', 'user delete'],
            ],
            [
                'base'     => 'role management',
                'children' => ['role add', 'role edit', 'role delete'],
            ],
            [
                'base'     => 'permission management',
                'children' => ['permission add', 'permission edit', 'permission delete'],
            ]
        ];

        // 1) Create permissions in desired order (base -> children)
        foreach ($modules as $m) {
            // base
            Permission::firstOrCreate([
                'name'       => $m['base'],
                'guard_name' => $guard,
            ]);

            // children
            foreach ($m['children'] as $child) {
                Permission::firstOrCreate([
                    'name'       => $child,
                    'guard_name' => $guard,
                ]);
            }
        }

        // 2) Create the 6 Roles
        $roles = [
            'Super Administrator',
            'Admin',
            'Store Manager',
            'Accountant',
            'Branch Accountant',
            'Sales Associate',
        ];

        $allPermissions = Permission::where('guard_name', $guard)->get();

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => $guard]);

            // Assign permissions
            if (in_array($roleName, ['Super Administrator', 'Admin'])) {
                $role->syncPermissions($allPermissions);
            } elseif ($roleName === 'Store Manager') {
                $role->syncPermissions(Permission::whereIn('name', [
                    'store management', 'store add', 'store edit',
                    'supplier management', 'supplier add', 'supplier edit',
                    'brand management', 'brand add', 'brand edit',
                    'category management', 'category add', 'category edit',
                    'unit management', 'unit add', 'unit edit',
                    'item management', 'item add', 'item edit',
                    'ingredient management', 'ingredient add', 'ingredient edit',
                    'food management', 'food add', 'food edit',
                ])->get());
            } elseif (in_array($roleName, ['Accountant', 'Branch Accountant'])) {
                $role->syncPermissions(Permission::whereIn('name', [
                    'store management',
                    'supplier management',
                    'brand management',
                    'category management',
                    'unit management',
                    'item management',
                    'ingredient management',
                    'food management',
                ])->get());
            } elseif ($roleName === 'Sales Associate') {
                $role->syncPermissions(Permission::whereIn('name', [
                    'brand management',
                    'category management',
                    'unit management',
                    'item management',
                    'ingredient management',
                    'food management',
                ])->get());
            }
        }

        // Cache refresh
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
