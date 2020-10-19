<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Project;
use App\Project_owner;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProjectAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:project-list|project-create|project-edit|project-delete', ['only' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->hasRole('Administrator'))
        {
            $data = Project::with('project_owner.user')->orderBy('id','DESC')->paginate(5);
        }
        else
            {
                if($owner = Project_owner::where('user_id', Auth::user()->id)->first())
                {
                  $data = Project::with('project_owner.user')->where('id', $owner->project_id)->orderBy('id','DESC')->paginate(5);
                }
                else return redirect()->route('admin')->with('status','There are no projects to manage');

            }
        return view('projectadmin.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $owner = new Project_owner();
        $owner->project_id = $request->project_id;
        $owner->user_id = $request->add_user_id;
        $owner->save();
        return redirect()->route('projectadmin.index')->with('success','User added successfully');;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $users = User::all();

        $project = Project::with('project_owner.user')->find($id);

        return view('projectadmin.edit',compact('project', 'users'));
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

        $project_owner = Project_owner::where('project_id', $id)->get();

        $i = 0;
        foreach ($project_owner as $owner)
        {
            $owner->user_id = $request->user_id[$i];
            $owner->save();
            $new_user = User::find($request->user_id[$i]);
            $old_user = User::find($request->old_user_id[$i]);
            //Transfer roles and permissions from old user -> new user
            $roles = $old_user->getRoleNames();
            $new_user->assignRole($roles);
            $permissions = $old_user->getAllPermissions();
            $new_user->syncPermissions($permissions);
            $i++;
        }

        return redirect()->route('projectadmin.index');
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
