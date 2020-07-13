<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectUpdate extends Model
{
    protected $dates = ['created_at'];
    protected $fillable = ['project_id', 'summary', 'comment', 'approved', 'reviewer_comment', 'internal_comment'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
