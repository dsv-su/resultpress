<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'description'];

    public function project_area()
    {
        return $this->hasMany(ProjectArea::class);
    }
}
