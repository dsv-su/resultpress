<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionResearchProjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'project-1-list',
            'project-1-update',
            'project-1-create',
            'project-1-edit',
            'project-1-delete',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
