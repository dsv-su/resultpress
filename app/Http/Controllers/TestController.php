<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use StudentAffairsUwm\Shibboleth\Entitlement;

class TestController extends Controller
{
    public function server()
    {
        $entitlement = 'urn:mace:uark.edu:ADGroups:Computing Services:Something';

        if (Entitlement::has($entitlement)) {
            // authorize something
        }
        dd($_SERVER);
    }
}
