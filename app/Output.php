<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    protected $fillable = ['indicator', 'status', 'target', 'project_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function output_updates()
    {
        return $this->hasMany(OutputUpdate::class);
    }
}
