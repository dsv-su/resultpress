<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
    public function server()
    {
        return $request->session()->all();
    }
}
