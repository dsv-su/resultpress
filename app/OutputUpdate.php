<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OutputUpdate extends Model
{
    protected $fillable = ['project_update_id', 'output_id', 'value'];

    public function output()
    {
        return $this->belongsTo(Output::class);
    }

    public function project_update() {
        return $this->belongsTo(ProjectUpdate::class);
    }
}
