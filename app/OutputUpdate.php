<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutputUpdate extends Model
{
    protected $fillable = ['project_update_id', 'output_id', 'value'];

    public function output(): BelongsTo
    {
        return $this->belongsTo(Output::class);
    }

    public function project_update(): BelongsTo
    {
        return $this->belongsTo(ProjectUpdate::class);
    }
}
