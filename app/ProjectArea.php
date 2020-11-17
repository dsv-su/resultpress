<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectArea extends Model
{

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

}
