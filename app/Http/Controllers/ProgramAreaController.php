<?php

namespace App\Http\Controllers;

use App\Area;
use App\Project;
use App\ProjectArea;
use App\ProjectPartner;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $program_areas = Area::all();
        if ($user = Auth::user()) {
            if ($user->hasRole(['Administrator', 'Program administrator', 'Spider'])) {
                $projects = Project::with('project_owner.user', 'project_area.area')->whereHas('project_area', function ($query) use ($id){
                    return $query->where('area_id', '=', $id);})->get();
                return view('project.index', ['projects' => $projects, 'user' => $user, 'program_areas' => $program_areas]);
            } elseif ($user->hasRole(['Partner'])) {
                $id = ProjectPartner::where('partner_id', $user->id)->pluck('project_id');
                $projects = Project::with('project_owner.user', 'project_area.area')->whereIn('id', $id)->latest()->get();

                return view('project.index', ['projects' => $projects, 'user' => $user, 'program_areas' => $program_areas]);
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
                $area = Area::find($id);
                return view('programareas.edit', compact('area'));
            }
        }
        abort(401);
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
                $area = Area::find($id);
                $area->name = $request->name;
                $area->description = $request->description;
                $area->save();
                return redirect()->route('programareas');
            }
        }
        abort(401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
