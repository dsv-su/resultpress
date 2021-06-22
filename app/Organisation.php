<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Organisation extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['org', 'address', 'website', 'phone', 'contact_project', 'contact_finance'];

    /**
     * The users that belong to the organisation.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_organisations');
    }
}
