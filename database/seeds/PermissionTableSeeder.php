<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'admin-list',
            'admin-update',
            'admin-create',
            'admin-edit',
            'admin-delete',
            'project-list',
            'project-update',
            'project-create',
            'project-edit',
            'project-delete',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
