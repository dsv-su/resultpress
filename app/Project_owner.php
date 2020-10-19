<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project_owner extends Model
{
    use LogsActivity;

    protected $fillable = ['project_id', 'user_id'];
    protected static $logAttributes =['project_id', 'user_id'];
    protected static $logName = 'ProjectOwner';
    protected static $logOnlyDirty = true;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
