<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class UserOrganisation extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['user_id', 'organisation_id'];
    protected static $logFillable = true;

}
