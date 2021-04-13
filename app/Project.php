<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    //protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id']; -->refactored<--
    protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'state'];
    protected $dates = ['start', 'end'];
    //protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id'];  -->refactored<--
    protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'state'];
    protected static $logName = 'Project';
    protected static $logOnlyDirty = true;

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function outputs()
    {
        return $this->hasMany(Output::class);
    }

    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }

    public function submitted_outputs()
    {
        return $this->outputs()->get()->filter(function ($output, $key) {
            return $output->status == 'custom' || $output->status == 'default';
        });
    }

    public function hasDraft()
    {
        return ($this->project_updates()->where('status', 'draft')->count() > 0);
    }

    public function project_updates()
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    public function pending_updates()
    {
        return $this->project_updates()->where('status', 'submitted')->get();
    }

    public function project_area()
    {
        return $this->hasMany(ProjectArea::class);
    }

    public function areas()
    {
        return $this->belongsToMany(Area::class, 'project_areas');
    }

    public function getCurrencySymbol()
    {
        switch ($this->currency) {
            case "USD":
                return '$';
            case "EUR":
                return '€';
            case "GBP";
                return '£';
            default:
                return 'kr';
        }
    }

    public function project_owner()
    {
        return $this->hasMany(ProjectOwner::class);
    }

    public function status()
    {
        $delayedhigh = 0;
        $delayednormal = 0;
        $completed = 0;

        foreach ($this->project_updates->sortBy('created_at', SORT_REGULAR, true) as $pu) {
            if ($pu->state == 'archived') {
                // Archived
                return 'archived';
            }

            if ($pu->state == 'terminated') {
                // Archived
                return 'terminated';
            }

            if ($pu->state == 'onhold') {
                // Archived
                return 'onhold';
            }
        }

        foreach ($this->activities() as $a) {
            if ($a->status() == 'delayedhigh') {
                $delayedhigh++;
            }
            if ($a->status() == 'delayednormak') {
                $delayednormal++;
            }
            if ($a->status() == 'completed') {
                $completed++;
            }
        }

        if ($delayedhigh) {
            // Delayed
            return 'delayedhigh';
        } elseif ($delayednormal) {
            return 'delayednormal';
        }

        if ($this->pending_updates()->count()) {
            // Pending review
            return 'pendingreview';
        }
        if ($this->start->gte(Carbon::now())) {
            // Pending
            return 'planned';
        }
        if ($this->start->lte(Carbon::now()) && $this->end->gte(Carbon::now())) {
            // In progress
            return 'inprogress';
        }
        if (($this->end->lte(Carbon::now())) && ($this->activities()->count() == $completed)) {
            // Finished
            return 'completed';
        }

    }
}


