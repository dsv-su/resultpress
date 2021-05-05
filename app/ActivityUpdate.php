<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityUpdate extends Model
{
    protected $dates = ['start', 'end'];
    protected $fillable = ['project_update_id', 'activity_id', 'money', 'comment', 'start', 'end', 'state'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function project_update() {
        return $this->belongsTo(ProjectUpdate::class);
    }
}
