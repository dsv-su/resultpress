<?php

namespace App\Http\Controllers;

use App\Services\ConfigurationHandler;

class SystemController extends Controller
{
    public function login()
    {
        return redirect('/shibboleth-authenticate');
    }

    public function start()
    {
        //Persist system configuration
        $init = new ConfigurationHandler();
        $init->system();
    }
}
