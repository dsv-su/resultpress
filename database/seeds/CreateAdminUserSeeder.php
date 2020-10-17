<?php

use Illuminate\Database\Seeder;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
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
        Permission::create(['name' => 'partner']);
        Role::create(['name' => 'Spider']);
        Role::create(['name' => 'Partner']);
        Role::create(['name' => 'Program administrator']);
    }

}
