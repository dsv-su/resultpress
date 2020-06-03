<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectUpdate extends Model
{
    protected $fillable = ['project_id', 'summary', 'comment', 'approved'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
