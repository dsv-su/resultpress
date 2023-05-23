<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProjectReminder extends Model
{
    use HasSlug;

    protected $fillable = ['project_id', 'name', 'reminder', 'reminder_due_days', 'set', 'type'];
    protected $cast = ['reminder_due_days' => 'integer'];
    protected $dates = ['set'];

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
     * Get the project that owns the reminder.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
