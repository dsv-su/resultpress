<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\URL;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Scopes\ObjectType;

class Project extends Model
{
    use LogsActivity;
    use SearchableTrait;

    //protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id']; -->refactored<--
    protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'state', 'object_type', 'object_id'];
    protected $dates = ['start', 'end'];
    //protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id'];  -->refactored<--
    protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'state'];
    protected static $logName = 'Project';
    protected static $logOnlyDirty = true;

    protected $searchable = [
        'columns' => [
            'name' => 10,
            'description' => 5
        ]
    ];

    protected $appends = ['link', 'type'];

    /**
     * Get the reminders for the project.
     * 
     * @return HasMany
     */
    public function reminders()
    {
        return $this->hasMany(ProjectReminder::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the project's outputs.
     * 
     * @return HasMany
     */
    public function outputs(): HasMany
    {
        return $this->hasMany(Output::class);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(Outcome::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ProjectHistory::class);
    }

    public function submitted_outputs(): Collection
    {
        return $this->outputs()->get()->filter(
            function ($output) {
            return $output->status == 'custom' || $output->status == 'default';
            }
        );
    }

    public function aggregated_outputs(): Collection
    {
        return $this->outputs()->get()->filter(
            function ($output) {
            return $output->status == 'aggregated';
            }
        );
    }


    public function drafts($pu = null): bool
    {
        if ($pu) {
            return $this->project_updates()->where('status', 'draft')->where('id', '<>', $pu->id)->count() > 0;
        }
        return $this->project_updates()->where('status', 'draft')->count() > 0;
    }

    public function project_updates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class);
    }

    public function pending_updates(): Collection
    {
        return $this->project_updates()->where('status', 'submitted')->get();
    }

    public function project_area(): BelongsToMany
    {
        return $this->BelongsToMany(Area::class, 'project_areas', 'project_id', 'area_id');
    }

    public function managers(): Collection
    {
        return $this->belongsToMany(User::class, 'project_owners', 'project_id', 'user_id')->get();
    }

    public function partners(): Collection
    {
        return $this->belongsToMany(User::class, 'project_partners', 'project_id', 'partner_id')->get();
    }

    public function project_partner(): HasMany
    {
        return $this->hasMany(ProjectPartner::class);
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'project_areas');
    }

    public function change_request(): HasOne
    {
        return $this->HasOne(self::class, 'object_id')->withoutGlobalScope(ObjectType::class)->where('object_type', 'project_change_request');
    }

    public function main(): BelongsTo
    {
        return $this->belongsTo(self::class, 'object_id')->withoutGlobalScope(ObjectType::class)->where('object_type', 'project');
    }

    public function getCurrencySymbol(): string
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

    public function project_owner(): HasMany
    {
        return $this->hasMany(ProjectOwner::class);
    }

    public function status(): string
    {
        $delayedhigh = 0;
        $delayednormal = 0;
        $completed = 0;

        foreach ($this->project_updates->sortBy('created_at', SORT_REGULAR, true) as $pu) {
            if ($pu->state == 'archived') {
                // Archived
                return 'archived';
            }

            if ($pu->state == 'terminated') {
                // Archived
                return 'terminated';
            }

            if ($pu->state == 'onhold') {
                // Archived
                return 'onhold';
            }
        }

        foreach ($this->activities as $a) {
            if ($a->status() == 'delayedhigh') {
                $delayedhigh++;
            }
            if ($a->status() == 'delayednormal') {
                $delayednormal++;
            }
            if ($a->status() == 'completed') {
                $completed++;
            }
        }
        if($this->object_type == 'project_change_request') {
            return 'pendingreview';
        }

        if ($this->object_type == 'project_add_request') {
            return 'pendingreview';
        }

        if ($delayedhigh) {
            // Delayed
            return 'delayedhigh';
        } elseif ($delayednormal) {
            return 'delayednormal';
        }

        if ($this->pending_updates()->count()) {
            // Pending review
            return 'pendingreview';
        }
        if ($this->start->gte(Carbon::now())) {
            // Pending
            return 'planned';
        }
        if ($this->start->lte(Carbon::now()) && (!$this->end || $this->end->gte(Carbon::now()))) {
            // In progress
            return 'inprogress';
        }
        if ($this->end && $this->end->lte(Carbon::now()) && $this->activities()->count() == $completed) {
            // Finished
            return 'completed';
        }
    }

    public function wrapJson()
    {
        foreach ($this->outputs as $i => $o) {
            if (!$o->status) {
                $this->outputs->forget($i);
            }
        }
        $this->makeHidden(['updated_at', 'created_at', 'type', 'link']);
        $this->outputs->makeHidden(['updated_at', 'created_at', 'project_id']);
        $this->activities->makeHidden(['updated_at', 'created_at', 'project_id', 'deleted_at']);
        $this->outcomes->makeHidden(['updated_at', 'created_at', 'project_id', 'user_id']);
        $this->project_updates->makeHidden(['updated_at', 'created_at', 'project_id']);
        foreach ($this->project_updates as $pu) {
            $pu->user = User::find($pu->user_id)->name;
            $pu->makeHidden(['updated_at', 'created_at', 'project_id']);
            $pu->activity_updates->makeHidden(['updated_at', 'created_at']);
            $pu->outcome_updates->makeHidden(['updated_at', 'created_at']);
            $pu->output_updates->makeHidden(['updated_at', 'created_at']);
            }
        $this->areas->makeHidden(['updated_at', 'created_at', 'pivot']);
        $this->project_owner->makeHidden(['updated_at', 'created_at', 'id']);
        $this->project_owner->each(
            function ($po) {
            $po->name = User::find($po->user_id)->name;
            }
        );
        $this->project_partner->makeHidden(['updated_at', 'created_at', 'id']);
        $this->project_partner->each(
            function ($p) {
            $p->name = User::find($p->partner_id)->name;
            }
        );

        $json = $this->toJson(JSON_PRETTY_PRINT);
        $previous = $this->histories()->orderBy('id', 'desc')->first()->data ?? null;
        if ($json != $previous) {
            return $json;
        }
        return false;
    }

    public function getNextProjectUpdateDate()
    {
        $lastprojectupdate = $this->project_updates->sortBy('end')->last(
            function ($pu) {
            return $pu->end;
            }
        );
        return $lastprojectupdate ? $lastprojectupdate->end->addDay()->format('d/m/Y') : Carbon::now()->format('d/m/Y');
    }

    public function getLinkAttribute(): string
    {
        return $this->attributes['link'] = URL::to('/') . '/project/' . $this->id;
    }

    public function getTypeAttribute(): string
    {
        return 'project';
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new ObjectType);
}

    /**
     * Scope a query to only include projects of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed                                 $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type = 'project')
    {
        return $query->where('object_type', $type);
    }

}
