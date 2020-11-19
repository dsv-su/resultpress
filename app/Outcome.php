<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outcome extends Model
{
    protected $dates = ['start', 'end'];
    protected $fillable = ['name', 'summary', 'outputs', 'completed', 'project_id', 'user_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}