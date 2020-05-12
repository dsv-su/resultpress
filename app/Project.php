<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'activities', 'status', 'outputs', 'aggregated_outputs'];
}
