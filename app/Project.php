<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status'];
    protected $dates = ['start', 'end'];
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
}


