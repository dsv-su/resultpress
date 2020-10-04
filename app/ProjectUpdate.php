<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectUpdate extends Model
{
    use LogsActivity;

    protected $dates = ['created_at'];
    protected $fillable = ['project_id', 'summary', 'comment', 'status', 'reviewer_comment', 'internal_comment'];
    protected static $logAttributes = ['project.name', 'summary', 'comment', 'status', 'reviewer_comment', 'internal_comment'];
    protected static $logName = 'ProjectUpdate';
    protected static $logOnlyDirty = true;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates() {
        return $this->hasMany(ActivityUpdate::class);
    }

    public function output_updates() {
        return $this->hasMany(OutputUpdate::class);
    }

    public function files() {
        return File::where(['filearea' => 'project_update', 'itemid' => $this->id]);
    }
}
