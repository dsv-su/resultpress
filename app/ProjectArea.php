<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectArea extends Model
{

    public function project()
    {
        return $this->hasMany(Project::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}
