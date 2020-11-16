<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'Administrator']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $role = Role::create(['name' => 'Program administrator']);
        $permissions = Permission::whereNotIn('id', [1, 2, 3, 4, 5])->pluck('id','id');
        $role->syncPermissions($permissions);
        $role = Role::create(['name' => 'Spider']);
        $permissions = Permission::whereNotIn('id', [1, 2, 3, 4, 5, 7, 9, 10, 11, 12, 13, 14])->pluck('id','id');
        $role->syncPermissions($permissions);
        $role = Role::create(['name' => 'Partner']);
        $permissions = Permission::create(['name' => 'partner']);
        $role->syncPermissions($permissions);
    }

}
