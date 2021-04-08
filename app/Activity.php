<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $dates = ['start', 'end'];
    protected $fillable = ['title', 'template', 'description', 'start', 'end', 'budget', 'project_id', 'reminder', 'reminder_due_days', 'priority'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates()
    {
        return $this->hasMany(ActivityUpdate::class);
    }

    public function status()
    {
        $completed = false;
        $pending = false;
        foreach ($this->activity_updates as $au) {
            if ($au->completed) {
                $completed = true;
            }
            if ($au->project_update->status == 'submitted') {
                $pending = true;
            }
        }
        if ($completed) {
            return 5;
        }
        if ($pending) {
            return 4;
        }
        if ($this->start->gte(Carbon::now()) && $this->activity_updates()->count() == 0) {
            return 1;
        }
        if ($this->start->lt(Carbon::now()) || $this->activity_updates()->count() > 0) {
            return 2;
        }
        if ($this->end->lt(Carbon::now())) {
            return 3;
        }
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