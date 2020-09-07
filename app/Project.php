<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description', 'start', 'end', 'activities', 'status', 'outputs', 'aggregated_outputs'];
    protected $dates = ['start', 'end'];
    protected static $logAttributes = ['name', 'description', 'start', 'end', 'activities', 'status', 'outputs', 'aggregated_outputs'];
    protected static $logName = 'Project';
    protected static $logOnlyDirty = true;

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function output()
    {
        return $this->hasMany(Output::class);
    }

    public function projectupdate()
    {
        return $this->hasMany(ProjectUpdate::class);
    }
}


