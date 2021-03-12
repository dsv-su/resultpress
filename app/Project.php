<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    //protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id']; -->refactored<--
    protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status'];
    protected $dates = ['start', 'end'];
    //protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id'];  -->refactored<--
    protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status'];
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

    /*public function user()
    {
        return $this->belongsTo(User::class);
    }*/
    public function project_owner()
    {
        return $this->hasMany(ProjectOwner::class);
    }

    public function status() {
        $delayed = false;
        if ($this->archived) {
            // Archived
            return 6;
        }
        foreach ($this->activities() as $a) {
            if ($a->status() == 3) {
                $delayed = true;
            }
        }
        if ($delayed) {
            // Delayed
            return 3;
        }
        if ($this->pending_updates()->count()) {
            // Pending review
            return 4;
        }
        if ($this->start->lt(Carbon::now())) {
            // Pending
            return 1;
        }
        if ($this->start->gte(Carbon::now())) {
            // In progress
            return 2;
        }
        if (($this->end->lte(Carbon::now()))) {
            // Finished
            return 5;
        }
        // Archived
    }
}


