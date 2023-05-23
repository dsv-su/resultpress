<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


class Output extends Model
{
    use HasSlug;

    protected $fillable = ['indicator', 'status', 'target', 'project_id'];
    protected $appends = ['valuesumnew', 'latest_update'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('indicator')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50)
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the project that owns the Output
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get all of the output updates for the Output
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function output_updates()
    {
        return $this->hasMany(OutputUpdate::class);
    }

    public function getLatestUpdateAttribute()
    {
        return $this->output_updates()->orderBy('created_at', 'desc')->first();
    }

    public function getValuesumnewAttribute()
    {
            $outputupdates = OutputUpdate::where('output_id', $this->id)
                ->join('project_updates', 'project_update_id', '=', 'project_updates.id')
                ->where('project_updates.status', 'approved')
                ->get(['output_updates.*']);
            $valuesum = 0;
            foreach ($outputupdates as $ou) {
                $valuesum += $ou->value;
            }
            return $valuesum;
    }
}
