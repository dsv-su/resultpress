<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invite extends Model
{
    use LogsActivity;

    protected $fillable = ['email', 'token', 'project_id'];
    protected static $logAttributes = ['email', 'token', 'project_id'];
    protected static $logName = 'Invite';
    protected static $logOnlyDirty = true;
}
