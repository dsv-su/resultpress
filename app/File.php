<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $dates = ['created_at'];
    protected $fillable = [
        'name',
        'file_path'
    ];
}
