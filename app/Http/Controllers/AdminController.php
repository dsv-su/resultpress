<?php

namespace App\Http\Controllers;


class AdminController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
            return redirect()->route('home')->withErrors(['You do not have permission to view this page.']);
        }
        return view('home.admin');
    }

}
