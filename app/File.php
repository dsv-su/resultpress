<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class File extends Model
{
    use LogsActivity;

    protected $dates = ['created_at'];
    protected $fillable = [
        'name',
        'file_path'
    ];
    protected static $logAttributes = ['name', 'file_path'];
    protected static $logName = 'File';
    protected static $logOnlyDirty = true;
}
