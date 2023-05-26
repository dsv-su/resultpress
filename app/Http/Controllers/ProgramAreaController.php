<?php

namespace App\Http\Controllers;

use App\Area;
use App\Project;
use App\ProjectArea;
use App\ProjectPartner;
use App\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramAreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('view-areas')) {
            return redirect()->route('home')->withErrors(['You do not have permission to view this page.']);
        }
        $programareas = Area::all();
        return view('programareas.index', compact('programareas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
                $users = User::whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Partner');
                })->orderBy('name')->get();
                return view('programareas.create', compact('users'));
            }
        }
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::check() && Auth::user()->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
            $area = new Area();
            $area->name = $request->name;
            $area->description = $request->description;
            $area->external_system_title = $request->external_system_title;
            $area->external_system_link = $request->external_system_link;
            $area->save();
            $area->users()->sync($request->user_id);
            return redirect()->route('programareas');
        } else {
            return redirect()->withErrors(['You do not have permission to create a program area.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Session $session)
    {
        $program_areas = Area::all();
        $area = Area::findOrfail($id);
        $shibboleth_token = $session->get('_token') ?? null;

        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
                $projects = Project::with('project_owner.user', 'areas')->whereHas('project_area', function ($query) use ($id) {
                    return $query->where('area_id', '=', $id);
                })->get();
                return view('project.index', ['projects' => $projects, 'user' => $user, 'program_areas' => $program_areas, 'area' => $area, 'token' => $shibboleth_token]);
            } elseif ($user->hasRole(['Partner'])) {
                $id = ProjectPartner::where('partner_id', $user->id)->pluck('project_id');
                $projects = Project::with('project_owner.user', 'areas')->whereIn('id', $id)->latest()->get();

                return view('project.index', ['projects' => $projects, 'user' => $user, 'program_areas' => $program_areas, 'area' => $area]);
            }
        } elseif (Auth::check()) return abort(403);
        else return redirect()->route('partner-login');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {

                $users = User::whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Partner');
                })->orderBy('name')->get();

                $area = Area::findOrfail($id);

                $areaUsers = $area->users->pluck('id')->toArray();

                return view('programareas.edit', compact('area', 'users', 'areaUsers'));
            }
        }
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
                $area = Area::findOrfail($id);
                $area->name = $request->name;
                $area->description = $request->description;
                $area->external_system_title = $request->external_system_title;
                $area->external_system_link = $request->external_system_link;
                $area->users()->sync($request->user_id);
                $area->save();
                return redirect()->route('programareas');
            }
        }
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Auth::check() && Auth::user()->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
            $area = Area::findOrfail($id);   
            $area->archive = 1;
            $area->save();     
            return redirect()->route('programareas');
        } else {
            return redirect()->withErrors(['You do not have permission to delete a program area.']);
        }
    }

    public function unarchive($id)
    {
        if(Auth::check() && Auth::user()->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
            $area = Area::findOrfail($id);   
            $area->archive = 0;
            $area->save();
            return redirect()->route('programareas');
        } else {
            return redirect()->withErrors(['You do not have permission to delete a program area.']);
        }
    }
}
