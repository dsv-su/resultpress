<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'activities', 'status', 'outputs', 'aggregated_outputs'];

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function output()
    {
        return $this->hasMany(Output::class);
    }
    public function projectupdate()
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}


