<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Hamedov\Taxonomies\HasTaxonomies;
use App\Scopes\RegulatorArea;

class Area extends Model
{
    use HasTaxonomies;

    protected $casts = [
        'archive' => 'boolean',
    ];
    protected $fillable = ['name', 'description', 'archive'];

    public function project_area()
    {
        return $this->hasMany(ProjectArea::class);
    }

    /**
     * The projects that belong to the area.
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_areas');
    }

    /**
     * The users that belong to the area.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'area_user');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new RegulatorArea);
    }
}
