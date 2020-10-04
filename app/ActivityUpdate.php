<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ActivityUpdate extends Model
{
    use LogsActivity;

    protected $dates = ['date'];
    protected $fillable = ['project_update_id', 'activity_id', 'status', 'money', 'comment', 'date'];
    protected static $logAttributes = ['project_update_id', 'activity_id', 'status', 'money', 'comment', 'date'];
    protected static $logName = 'ActivityUpdate';
    protected static $logOnlyDirty = true;

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function project_update() {
        return $this->belongsTo(ProjectUpdate::class);
    }
}
