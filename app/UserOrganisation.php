<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrganisation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'organisation_id'];
}
