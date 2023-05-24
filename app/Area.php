<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $casts = [
        'archive' => 'boolean',
    ];
    protected $fillable = ['name', 'description', 'archive'];

    public function project_area()
    {
        return $this->hasMany(ProjectArea::class);
    }

    /**
     * The projects that belong to the area.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_areas');
    }

    /**
     * The users that belong to the area.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'area_user');
    }


}
