<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ProjectUpdate extends Model
{

    protected $dates = ['created_at'];
    protected $fillable = ['project_id', 'summary', 'comment', 'status', 'state', 'reviewer_comment', 'internal_comment'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates(): HasMany
    {
        return $this->hasMany(ActivityUpdate::class);
    }

    public function output_updates(): HasMany
    {
        return $this->hasMany(OutputUpdate::class);
    }

    public function outcome_updates(): HasMany
    {
        return $this->hasMany(OutcomeUpdate::class);
    }

    public function files()
    {
        return File::where(['filearea' => 'project_update', 'itemid' => $this->id]);
    }

    public function editable(): bool
    {
        if (Auth::user()->hasRole('Administrator') || ($this->status == 'draft' && Auth::user()->id == $this->user_id)) {
            return true;
        }
        return false;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIndex(): int
    {
        $i = 1;
        foreach ($this->project->project_updates as $pu) {
            if ($this->id == $pu->id) {
                return $i;
            }
            $i++;
        }
        return 0;
    }
}