<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    public function index()
    {
        $activity = Activity::all();
        return view('logs.index',['activities' => $activity]);
    }
}
