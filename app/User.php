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
        'name', 'email', 'password',
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

    protected static $logAttributes = [
        'name', 'email',
    ];
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
     * Get all of the prganisations for the user.
     */
    public function organisations()
    {
        return $this->belongsToMany(Organisation::class, 'user_organisations');
    }


}
