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

    public function histories()
    {
        return $this->hasMany(ProjectHistory::class);
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
            if ($a->status() == 'delayednormal') {
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

    public function wrapJson() {
        foreach ($this->outputs as $o) {
            $o->makeHidden('updated_at');
            $o->makeHidden('created_at');
            $o->makeHidden('project_id');
        }
        foreach ($this->activities as $a) {
            $a->makeHidden('updated_at');
            $a->makeHidden('created_at');
            $a->makeHidden('project_id');
            $a->makeHidden('deleted_at');
        }
        foreach ($this->outcomes as $o) {
            $o->makeHidden('updated_at');
            $o->makeHidden('created_at');
            $o->makeHidden('user_id');
            $o->makeHidden('project_id');
        }
        foreach ($this->project_updates as $pu) {
            $pu->makeHidden('project_id');
            $pu->makeHidden('updated_at');
            $pu->makeHidden('created_at');
            foreach ($pu->activity_updates as $au) {
                $au->makeHidden('updated_at');
                $au->makeHidden('created_at');
            }
            foreach ($pu->outcome_updates as $ou) {
                $ou->makeHidden('updated_at');
                $ou->makeHidden('created_at');
            }
            foreach ($pu->output_updates as $ou) {
                $ou->makeHidden('updated_at');
                $ou->makeHidden('created_at');
            }
        }
        $this->makeHidden('updated_at');
        $this->makeHidden('created_at');

        $json = $this->toJson(JSON_PRETTY_PRINT);
        $previous = $this->histories()->orderBy('id', 'desc')->first()->data ?? null;
        if ($json != $previous) {
            return $json;
        }
        return false;
    }
}


