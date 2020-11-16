<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectArea extends Model
{

    protected $fillable = ['name'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

}
