<?php

namespace App\Http\Controllers;

use App\Project;
use App\ProjectOwner;
use App\ProjectPartner;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class ProjectAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:project-list|project-create|project-edit|project-delete', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|RedirectResponse|View
     */
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('Administrator')) {
            $data = Project::with('project_owner.user')->orderBy('id', 'DESC')->get();
        } else {
            if ($owner_id = ProjectOwner::where('user_id', Auth::user()->id)->pluck('project_id')) {
                $data = Project::with('project_owner.user')->whereIn('id', $owner_id)->orderBy('id', 'DESC')->get();
            } else return redirect()->route('admin')->with('status', 'There are no projects to manage');

        }
        return view('projectadmin.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $owner = new ProjectOwner();
        $owner->project_id = $request->project_id;
        $owner->user_id = $request->add_user_id;
        $owner->save();
        return redirect()->route('projectadmin.index')->with('success', 'User added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|Response|View
     */
    public function edit(int $id)
    {
        $users = User::all();
        $partners = ProjectPartner::where('project_id', $id)->pluck('partner_id')->toArray();
        $project = Project::with('project_owner.user')->find($id);
        $old_users = ProjectOwner::where('project_id', $id)->pluck('user_id')->toArray();


        return view('projectadmin.edit', compact('project', 'users', 'old_users', 'partners'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id)
    {

        $project_owners = ProjectOwner::where('project_id', $id)->get();
        $project_partners = ProjectPartner::where('project_id', $id)->get();
        //Erase existing owners
        foreach($project_owners as $project_owner)
        {
            $owner = ProjectOwner::find($project_owner->id);
            $user = User::find($owner->user_id);
            $user->revokePermissionTo('project-'.$id.'-list');
            $user->revokePermissionTo('project-'.$id.'-edit');
            $user->revokePermissionTo('project-'.$id.'-update');
            $user->revokePermissionTo('project-'.$id.'-delete');
            $owner->delete();
        }
        //Store new managers
       foreach ($request->user_id as $owner)
        {
            $new_owner = new ProjectOwner();
            $new_owner->project_id = $id;
            $new_owner->user_id = $owner;
            $new_owner->save();
            //Give specific project permissions to user
            $user = User::find($owner);
            $user->givePermissionTo('project-'.$id.'-list', 'project-'.$id.'-edit', 'project-'.$id.'-update', 'project-'.$id.'-delete');
        }
       //Erase existing partners
        if($project_partners)
        {
            foreach($project_partners as $project_partner)
            {
                $partner = ProjectPartner::find($project_partner->id);
                $user = User::find($partner->partner_id);
                $user->revokePermissionTo('project-'.$id.'-list');
                $user->revokePermissionTo('project-'.$id.'-update');
                $partner->delete();
            }
        }
        //Store new partners
        if($request->partner_id)
        {
            foreach ($request->partner_id as $partner)
            {
                $new_partner = new ProjectPartner();
                $new_partner->project_id = $id;
                $new_partner->partner_id = $partner;
                $new_partner->save();
                //Give specific project permissions to partner
                $user = User::find($partner);
                $user->givePermissionTo('project-'.$id.'-list', 'project-'.$id.'-update');
            }
        }

        return redirect()->route('projectadmin.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id)
    {
        //
    }
}
