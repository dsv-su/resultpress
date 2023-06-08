<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\TaxonomyType;

class AddNewUserRoleWithItsPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
        $permission = Permission::where('name', 'reviewer');
        if (!$permission->count()) {
            $permission = Permission::create([
                'name' => 'reviewer',
                'guard_name' => 'web'
            ]);
        }

        // Add new user role with its permissions if not exists
        $role = Role::where('name', 'Regulator Admin');
        if (!$role->count()) {
            $role = Role::create([
                'name' => 'Regulator Admin',
                'guard_name' => 'web'
            ]);

            $role->syncPermissions([
                'project-list',
                'project-create',
                'project-edit',
                'project-update',
                'view-areas',
                'reviewer',
            ]);
        }

        $role = Role::where('name', 'Regulator User');
        if (!$role->count()) {
            $role = Role::create([
                'name' => 'Regulator User',
                'guard_name' => 'web'
            ]);

            $role->syncPermissions([
                'project-list',
                'project-create',
                'project-edit',
                'project-update',
            ]);
        }

        // Create some basic taxonomy types
        $types = ['Category', 'Region', 'Language', 'Regulators Area'];
        foreach ($types as $type) {
            $taxonomyType = TaxonomyType::where('name', $type);
            if (!$taxonomyType->count()) {
                $taxonomyType = TaxonomyType::create([
                    'name' => $type,
                    'description' => $type,
                    'model' => $type == 'Regulators Area' ? 'Area' : 'Project',
                ]);

                if ($type == 'Regulators Area' && $taxonomyType->taxonomies()->count() == 0) {
                    $taxonomyType->taxonomies()->create([
                        'title' => 'Regulator Area',
                        'slug' => 'regulator-area',
                        'parent_id' => null,
                    ]);
                    $taxonomyType->taxonomies()->create([
                        'title' => 'Not Regulator Area',
                        'slug' => 'not-regulator-area',
                        'parent_id' => null,
                    ]);
                }
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete all permissions
        $permissions = Permission::whereIn('name', [
            'reviewer',
        ])->get();
        foreach ($permissions as $permission) {
            $permission->delete();
        }

        // Delete all roles
        $roles = Role::whereIn('name', [
            'Regulator Admin',
            'Regulator User',
        ])->get();
        foreach ($roles as $role) {
            $role->delete();
        }
    }
}
