<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ObjectType implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * This scope will only be applied to the Project model to filter out all projects that are not of type 'project'.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('object_type', 'project');
    }
}