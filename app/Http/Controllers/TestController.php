<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StudentAffairsUwm\Shibboleth\Entitlement;

class TestController extends Controller
{
    public function server()
    {
        dd($_SERVER);
    }
}
