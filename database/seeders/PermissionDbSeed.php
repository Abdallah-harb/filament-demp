<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionDbSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'dashboard',
            'category',
            'create_category',
            'edit_category',
            'delete_category',
            'tags',
            'create_tags',
            'edit_tags',
            'delete_tags',
            'products',
            'create_products',
            'edit_products',
            'delete_products',
            'orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'users',
            'create_users',
            'edit_users',
            'delete_users',
            'roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'settings',
            'edit_settings',
            'invoices',
            'reports',
            'create_report',
            'edit_report',
            'delete_report',
            'view_report',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
