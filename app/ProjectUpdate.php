<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ProjectUpdate extends Model
{

    protected $dates = ['created_at'];
    protected $fillable = ['project_id', 'summary', 'comment', 'status', 'state', 'reviewer_comment', 'internal_comment'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates()
    {
        return $this->hasMany(ActivityUpdate::class);
    }

    public function output_updates()
    {
        return $this->hasMany(OutputUpdate::class);
    }

    public function outcome_updates()
    {
        return $this->hasMany(OutcomeUpdate::class);
    }

    public function files()
    {
        return File::where(['filearea' => 'project_update', 'itemid' => $this->id]);
    }

    public function editable()
    {
        if (Auth::user()->hasRole('Administrator') || ($this->status == 'draft' && Auth::user()->id == $this->user_id)) {
            return true;
        } else {
            return false;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}