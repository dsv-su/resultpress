<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class System extends Model
{
    use LogsActivity;

    protected $fillable = ['app_env', 'app_debug', 'app_url', 'authorization_parameter','authorization','login_route', 'db', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password'];
    protected static $logAttributes = ['app_env', 'app_debug', 'app_url', 'authorization_parameter','authorization','login_route', 'db', 'db_host', 'db_port', 'db_database', 'db_username', 'db_password'];
    protected static $logOnlyDirty = true;
}
