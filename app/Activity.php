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
        foreach ($this->activity_updates->sortBy('created_at', SORT_REGULAR, true) as $au) {
            if ($au->project_update->status == 'approved') {
                if ($au->state == 'completed') {
                    return 'completed';
                }
                if ($au->state == 'cancelled') {
                    return 'cancelled';
                }
            }

            if ($au->project_update->status == 'submitted') {
                return 'pendingreview';
            }
        }

        if ($this->start->gte(Carbon::now()) && $this->activity_updates()->count() == 0) {
            return 'planned';
        }
        if ($this->start->lt(Carbon::now()) || $this->activity_updates()->count() > 0) {
            return 'inprogress';
        }
        if ($this->end->lt(Carbon::now())) {
            if ($this->priority == 'high') {
                return 'delayedhigh';
            } else {
                return 'delayednormal';
            }

        }

        return 'finished';
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