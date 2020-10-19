<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project_partner extends Model
{
    use LogsActivity;

    protected $fillable = ['project_id', 'partner_id'];
    protected static $logAttributes = ['project_id', 'partner_id'];
    protected static $logName = 'ProjectPartner';
    protected static $logOnlyDirty = true;
}
