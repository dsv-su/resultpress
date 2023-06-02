<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class RegulatorArea implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * This scope will only be applied to the Area model to filter out all areas that are regulator areas.
     *
     * @param  Builder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (Auth::user()->isRegulatorAdmin || Auth::user()->isRegulator) {
            $builder->whereHas('taxonomies', function ($query) {
                $query->where('type', 'regulators-area')->where('slug', 'regulator-area');
            });
        }
    }
}