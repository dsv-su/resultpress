<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class TaxonomyType extends Model
{
    use HasFactory, HasSlug;

    /**
     * The fillable attributes.
     *
     * @var string
     */
    protected $fillable = [
        'name',
        'slug',
        'model',
    ];

    /**
     * Slaggable options.
     *
     * @var string
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the taxonomies for the taxonomy type.
     */
    public function taxonomies()
    {
        return $this->hasMany(config('taxonomies.taxonomies_model', Hamedov\Taxonomies\Taxonomy::class), 'type', 'slug');
    }

    /**
     * Get the taxonomies for the taxonomy type in tree format.
     */
    public function taxonomiesTree($parent = null)
    {
        $taxonomies = $parent ? $this->taxonomies()->where('parent_id', $parent)->get() : $this->taxonomies()->whereNull('parent_id')->get();
        $taxonomies->each(function ($taxonomy) {
            $taxonomy->children = $this->taxonomies()->where('parent_id', $taxonomy->id)->get();
        });
        return $taxonomies;
    }

    /**
     * Get the parent taxonomy for a given taxonomy.
     */
    public function getParentTaxonomy($id = null)
    {
        $taxonomy = config('taxonomies.taxonomies_model', Hamedov\Taxonomies\Taxonomy::class)::find($id);
        if(!$taxonomy) $taxonomy = $this->taxonomies()->first();
        return $taxonomy->parent_id ? $this->taxonomies()->find($taxonomy->parent_id)->first()->id : null;
    }

    public function getModels()
    {
        return [
            'Project',
            'ProjectUpdate',
            'Area',
            'Organisation',
        ];
    }

    /**
     * Build html select input of taxonomies.
     */     
    public function taxonomiesHtmlSelect($instenceField = null, $instence = null, $id = null, $parent = null)
    {
        $checkSelected = $parent;
        if($instenceField && $instence) {
            $checkSelected = $instence->$instenceField;
        }
        $taxonomies = $this->taxonomiesTree();
        $html  = sprintf('<select name="%s" class="form-control">', $instenceField ? $instenceField : 'parent_id'); 
        $html .= '<option value="0">Root</option>';
        $html .= $this->buildOptionsTree($taxonomies, $id, $checkSelected);
        $html .= '</select>';
        return $html;
    }

    /**
     * Build html options tree of taxonomies.
     */
    public function buildOptionsTree($taxonomies, $id = null, $parent = null, $subLevels = 0)
    {
        $html = '';
        $subPrefix = '';
        if($parent && !is_array($parent)) $parent = [$parent];
        if ($subLevels > 0) {
            $subPrefix = str_repeat('--', $subLevels) . ' ';
        }
        foreach($taxonomies as $taxonomy) {
            if ($id && ($id == $taxonomy->id || $id == $taxonomy->parent_id)) {} else {
                $selected = $parent && is_array($parent) && in_array($taxonomy->id, $parent) ? 'selected' : '';
                $html .= sprintf('<option %s value="%s">%s%s</option>', $selected, $taxonomy->id, $subPrefix, $taxonomy->title);
            };
            if($taxonomy->children->count()) {
                $subLevels++;
                $html .= $this->buildOptionsTree($taxonomy->children, $id, $parent, $subLevels);
            }
        }
        return $html;
    }
}
