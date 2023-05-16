<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectReminder extends Model
{
    protected $fillable = ['project_id', 'name', 'reminder', 'reminder_due_days', 'set', 'type'];
    protected $cast = ['reminder_due_days' => 'integer'];
    protected $dates = ['set'];

    /**
     * Get the project that owns the reminder.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
