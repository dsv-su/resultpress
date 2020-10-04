<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class OutputUpdate extends Model
{
    use LogsActivity;

    protected $fillable = ['project_update_id', 'output_id', 'value'];
    protected static $logAttributes = ['project_update_id', 'output_id', 'value'];
    protected static $logName = 'OutputUpdate';
    protected static $logOnlyDirty = true;

    public function output()
    {
        return $this->belongsTo(Output::class);
    }

    public function project_update() {
        return $this->belongsTo(ProjectUpdate::class);
    }
}
