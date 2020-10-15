<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $dates = ['start', 'end'];
    protected $fillable = ['title', 'template', 'description', 'start', 'end', 'budget', 'project_id', 'reminder', 'reminder_due_days'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates()
    {
        return $this->hasMany(ActivityUpdate::class);
    }

    public function getComment()
    {
        if ($this->project->cumulative && !$this->activity_updates->isEmpty()) {
            return $this->activity_updates->last()->comment;
        } else {
            return $this->template;
        }
    }
}