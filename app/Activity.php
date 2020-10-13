<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $dates = ['start', 'end'];
    protected $fillable = ['title', 'description', 'start', 'end', 'budget', 'project_id', 'reminder', 'reminder_due_days'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates()
    {
        return $this->hasMany(ActivityUpdate::class);
    }
}
