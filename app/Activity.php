<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Activity extends Model
{
    use SoftDeletes, HasSlug;

    protected $dates = ['start', 'end'];
    protected $fillable = ['title', 'template', 'description', 'start', 'end', 'budget', 'project_id', 'priority', 'slug'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->allowDuplicateSlugs()
            ->skipGenerateWhen(fn () => $this->slug !== null)
            ->doNotGenerateSlugsOnUpdate();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function activity_updates(): HasMany
    {
        return $this->hasMany(ActivityUpdate::class);
    }

    public function status(): string
    {
        foreach ($this->activity_updates->sortBy('created_at', SORT_REGULAR, true) as $au) {
            if ($au->project_update->status == 'approved') {
                if ($au->state == 'completed') {
                    return 'completed';
                }
                if ($au->state == 'cancelled') {
                    return 'cancelled';
                }
            }

            if ($au->project_update->status == 'submitted') {
                return 'pendingreview';
            }
        }

        if ($this->end->lt(Carbon::now())) {
            if ($this->priority == 'high') {
                return 'delayedhigh';
            } else {
                return 'delayednormal';
            }
        }

        if ($this->start->gte(Carbon::now()) && $this->activity_updates()->count() == 0) {
            return 'planned';
        }

        if ($this->start->lt(Carbon::now()) || $this->activity_updates()->count() > 0) {
            return 'inprogress';
        }

        return 'finished';
    }

    public function getComment()
    {
        if ($this->project->cumulative && !$this->activity_updates->isEmpty()) {
            return $this->activity_updates->last()->comment;
        } else {
            return $this->template;
        }
    }
}
