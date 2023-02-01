<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectReminder extends Model
{
    protected $fillable = ['project_id', 'name', 'reminder', 'reminder_due_days', 'set'];
    protected $dates = ['set'];
}
