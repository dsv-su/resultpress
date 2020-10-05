<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Activity extends Model
{
    use SoftDeletes, LogsActivity;

    protected $dates = ['start', 'end'];
    protected $fillable = ['title', 'description', 'start', 'end', 'budget', 'project_id'];
    protected static $logAttributes = ['title', 'description', 'start', 'end', 'budget', 'project.name'];
    protected static $logName = 'Activity';
    protected static $logOnlyDirty = true;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates()
    {
        return $this->hasMany(ActivityUpdate::class);
    }
}
