<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, LogsActivity, CausesActivity, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'follow_projects', 'setting'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['fullviewname', 'nameWithOrg'];

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    /*public function project()
    {
        return $this->hasMany(User::class);
    }*/
    public function project_owner()
    {
        return $this->hasMany(ProjectOwner::class);
    }

    public function project_updates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    /**
     * Get all of the projects for the user.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_owners');
    }

    /**
     * Get all of the projects for the user (Partner).
     */
    public function partner_projects()
    {
        return $this->belongsToMany(Project::class, 'project_partners', 'partner_id', 'project_id');
    }

    /**
     * Get all of the prganisations for the user.
     */
    public function organisations()
    {
        return $this->belongsToMany(Organisation::class, 'user_organisations');
    }

    /**
     * Get all of the areas for the user.
     */
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'area_user');
    }

    /**
     * Get the name with organisation and role for the user.
     */
    public function getFullviewnameAttribute()
    {   
        $organisations = '';
        if(!empty($this->organisations)) {
            $organisations = ' - ' . $this->organisations->pluck('org')->implode(', ');
        }
        $roles = '';
        if(!empty($this->roles)) {
            $roles = ' - ' . $this->roles->pluck('name')->implode(', ');
        }
        return $this->name . $organisations . $roles;
    }

    /**
     * Get name with organisation for the user.
     */
    public function getNameWithOrgAttribute()
    {   
        $organisations = '';
        if(!empty($this->organisations)) {
            $organisations = ' - ' . $this->organisations->pluck('org')->implode(', ');
        }
        return $this->name . $organisations;
    }


}
