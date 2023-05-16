<?php

namespace App\Http\Controllers;


class AdminController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('system-admin')) {
            return redirect()->route('home')->withErrors(['You do not have permission to view this page.']);
        }
        return view('home.admin');
    }

}
