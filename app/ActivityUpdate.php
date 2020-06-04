<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActivityUpdate extends Model
{
    protected $fillable = ['project_update_id', 'activity_id', 'status', 'money', 'comment', 'date'];
}
