<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outcome extends Model
{
    protected $fillable = ['name', 'project_id', 'user_id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function outcome_updates(): HasMany
    {
        return $this->hasMany(OutcomeUpdate::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completed() {
        foreach ($this->outcome_updates as $ou) {
            if ($ou->completed_on && $ou->project_update->status == 'approved') {
                return $ou->completed_on;
            }
        }
        return false;
    }

    public function latest_approved_update() {
        foreach ($this->outcome_updates->sortBy('created_at', SORT_REGULAR, true) as $ou) {
            if ($ou->project_update->status == 'approved') {
                return $ou;
            }
        }
        return false;
    }
}