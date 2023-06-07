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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\Traits\LogsActivity;
use Hamedov\Taxonomies\HasTaxonomies;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Scopes\ObjectType;

class Project extends Model
{
    use LogsActivity, HasSlug, HasTaxonomies;

    //protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id']; -->refactored<--
    protected $fillable = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'state', 'object_type', 'object_id', 'summary', 'overall_budget'];
    protected $dates = ['start', 'end'];
    //protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'status', 'project_area_id'];  -->refactored<--
    protected static $logAttributes = ['name', 'description', 'template', 'start', 'end', 'currency', 'cumulative', 'state'];
    protected static $logName = 'Project';
    protected static $logOnlyDirty = true;

    protected $appends = ['link', 'type', 'isRegulator', 'totalSpent'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get taxonomies class.
     */
    public function taxClass(): string
    {
        return config('taxonomies.taxonomies_model', Hamedov\Taxonomies\Taxonomy::class);
    }

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

    /**
     * Get the comments for the project.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
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
        } elseif ($completed) {
            return 'completed';
        } else {
            return 'inprogress';
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

    public function getIsRegulatorAttribute(): bool
    {
        return $this->areas()->whereHas('taxonomies', function ($query) {
            $query->where('type', 'regulators-area')->where('slug', 'regulator-area');
        })->exists();
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->project_updates->where('status', 'approved')->sum('overall_spent');
    }

    // Spent percentage of overall budget
    public function getSpentPercentageAttribute(): float
    {
        return $this->totalSpent / $this->overall_budget * 100;
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
     * @param  mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type = 'project')
    {
        return $query->where('object_type', $type);
    }

    /**
     * Scope a query to only include projects of regulators.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRegulatorProject(Builder $builder)
    {
        return $builder->whereHas('areas', function ($query) {
            $query->whereHas('taxonomies', function ($query) {
                $query->where('type', 'regulators-area')->where('slug', 'regulator-area');
            });
        });
    }

    public function getHistory( $attribute = 'name', $id = null)
    {
        $history = $this->histories()->orderBy('id', 'desc')->skip(1)->first();
        if ($history) {
            $data = json_decode($history->data ?? '{}', true);
            $data = collect($data);
            if($attribute == 'areas' && is_array($data->get($attribute)) && ! empty($data->get($attribute))) {
                $historyIds = array_column($data->get($attribute), 'id');
                $currentIds = array_column($this->$attribute->toArray(), 'id');
                $changedIds = array_diff($historyIds, $currentIds);
                $oldValues = array_column($data->get($attribute), 'name');
                if (! empty($changedIds) ) {
                    return 'Previous: ' . implode(', ', $oldValues);
                }
            }
            if(is_string($data->get($attribute)) && $data->get($attribute) !== $this->$attribute) {
                return 'Previous: ' . $data->get($attribute);
            }
        }
        return null;
    }

    public function getSuggestedChanges( $attribute = 'name', $id = null, $field = null ) {
        $attributesTypes = [
            'basic' => [
                'name',
                'summary',
                'description',
                'start',
                'end',
            ],
            'selections' => [
                'areas',
                'project_owner',
                'project_partner',
                'currency',
                'cumulative',
            ],
            'subitems' => [
                'activities',
                'reminders',
                'deadlines',
                'outcomes',
                'outputs',
            ],
        ];
        $objectType = $this->object_type;
        if($objectType === 'project_change_request'){
            $originalProject = $this->main;
            if($originalProject){
                if(in_array($attribute, $attributesTypes['basic']) && (is_string($this->$attribute) || strtotime($this->$attribute)) && $this->$attribute !== $originalProject->$attribute) {
                    $return = strtotime($originalProject->$attribute) ? Carbon::parse($originalProject->$attribute)->format('Y-m-d') : $originalProject->$attribute;
                    return 'Previous: ' . $return;
                }
                if(in_array($attribute, $attributesTypes['subitems'])){
                    //$field = $attribute === 'activities' ? 'title' : ($attribute === 'outputs' ? 'indicator' : 'name');
                    if($id === null){
                        // What has been deleted?
                        $originalNames = $originalProject->$attribute->pluck('slug');
                        $currentNames = $this->$attribute->pluck('slug');
                        $deletedNames = $originalNames->diff($currentNames);
                        $deletedModels = $originalProject->$attribute->whereIn('slug', $deletedNames);
                        if($deletedNames->count() > 0){
                            return $deletedModels;
                        }
                    } else {
                        // What has been changed?
                        $originalModel = $originalProject->$attribute->where('slug', $id)->first();
                        $currentModel = $this->$attribute->where('slug', $id)->first();
                        if($originalModel && $currentModel){
                            $isDate = false;
                            try {
                                $originalDate = Carbon::createFromFormat('Y-m-d H:i:s', $originalModel->$field)->format('Y-m-d');
                                $currentData = Carbon::createFromFormat('Y-m-d H:i:s', $currentModel->$field)->format('Y-m-d');
                                $isDate = true;
                            } catch (\Throwable $th) {}
                            if ($isDate && $originalDate !== $currentData) {
                                return 'Previous: ' . Carbon::parse($originalModel->$field)->format('Y-m-d');
                            }
                            if (!$isDate && $originalModel->$field !== $currentModel->$field) {
                                return 'Previous: ' . $originalModel->$field;
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

}
