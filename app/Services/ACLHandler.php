<?php

namespace App\Services;


use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ACLHandler extends Model
{
    protected $permission, $project, $user;
    private $admin, $program_admin, $spider;

    public function __construct($project, $user)
    {
        $this->project = $project;
        $this->user = $user;
    }

    private function setRoles()
    {
        //Administrator: can do anything on any project
        $this->admin = Role::where('name', 'Administrator')->get();
        //Program administrator: can do anything on any project linked to that program (once program areas get implemented)
        $this->program_admin = Role::where('name', 'Program administrator')->get();
        //Spider: can read anything and full write access to specified projects
        $this->spider = Role::where('name', 'Spider')->get();
    }

    public function setNewProjectPermissions()
    {
        $this->setRoles();
        //Read
        $this->permission = Permission::create(['name' => 'project-' . $this->project->id . '-list']);
        $this->permission->assignRole($this->admin); //Administrator
        $this->permission->assignRole($this->program_admin); //Program administrator
        $this->permission->assignRole($this->spider); //Spider
        //Edit
        $this->permission = Permission::create(['name' => 'project-' . $this->project->id . '-edit']);
        $this->permission->assignRole($this->admin); //Administrator
        $this->permission->assignRole($this->program_admin); //Program administrator
        $this->user->givePermissionTo($this->permission); //Logged in user
        //Update
        $this->permission = Permission::create(['name' => 'project-' . $this->project->id . '-update']);
        $this->permission->assignRole($this->admin); //Administrator
        $this->permission->assignRole($this->program_admin); //Program administrator
        $this->user->givePermissionTo($this->permission); //Logged in user
        //Delete
        $this->permission = Permission::create(['name' => 'project-' . $this->project->id . '-delete']);
        $this->permission->assignRole($this->admin); //Administrator
        $this->permission->assignRole($this->program_admin); //Program administrator
        $this->user->givePermissionTo($this->permission); //Logged in user
    }
}
