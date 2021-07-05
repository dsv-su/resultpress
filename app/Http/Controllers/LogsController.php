<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class LogsController extends Controller
{
    public function index()
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator'])) {
                abort(401);
            }
        }
        $activity = Activity::all();
        return view('logs.index',['activities' => $activity]);
    }
}
