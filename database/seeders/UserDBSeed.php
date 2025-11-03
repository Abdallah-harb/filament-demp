<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserDBSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user =User::find(1);
        $role = Role::create(['name' => 'Super admin']);
        $permissions = Permission::all()->pluck('id')->toArray();
        $role->permissions()->sync($permissions);
        $user->assignRole($role);
    }
}
