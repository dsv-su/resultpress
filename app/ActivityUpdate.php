<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityUpdate extends Model
{
    protected $fillable = ['project_update_id', 'activity_id', 'money', 'comment', 'state'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function project_update() {
        return $this->belongsTo(ProjectUpdate::class);
    }
}
