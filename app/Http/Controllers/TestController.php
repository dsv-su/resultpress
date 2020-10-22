<?php

namespace App\Http\Controllers;

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
