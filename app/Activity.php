<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $dates = ['start', 'end'];
    protected $fillable = ['title', 'description', 'start', 'end', 'budget', 'project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
