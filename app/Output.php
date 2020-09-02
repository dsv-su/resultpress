<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Output extends Model
{
    use LogsActivity;

    protected $fillable = ['indicator', 'target', 'project_id'];
    protected static $logAttributes = ['indicator', 'target', 'project.name'];
    protected static $logName = 'Output';
    protected static $logOnlyDirty = true;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
