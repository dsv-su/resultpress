<?php

namespace App\Http\Controllers;

use App\Invite;
use App\Mail\PartnerInvite;
use App\Notifications\InviteNotification;
use App\Organisation;
use App\Project;
use App\User;
use DB;
use Hash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('registration_view');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */

    public function index(Request $request)
    {
        $data = User::orderBy('id', 'DESC')->get();
        return view('users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            //'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
        $input = $request->all();
        if (empty($input['password'])) {
            $salt = Str::random(8);
            $input['password'] = Hash::make($salt);
        } else $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        if ($user->hasRole('Administrator')) {
            $user->givePermissionTo('admin-list');
            $user->givePermissionTo('admin-update');
            $user->givePermissionTo('admin-create');
            $user->givePermissionTo('admin-edit');
            $user->givePermissionTo('admin-delete');
            $user->givePermissionTo('project-list');
            $user->givePermissionTo('project-update');
            $user->givePermissionTo('project-create');
            $user->givePermissionTo('project-edit');
            $user->givePermissionTo('project-delete');
            //For logging out
            $user->givePermissionTo('partner');
        } elseif ($user->hasRole('Program administrator')) {
            $user->givePermissionTo('project-list');
            $user->givePermissionTo('project-update');
            $user->givePermissionTo('project-create');
            $user->givePermissionTo('project-edit');
            $user->givePermissionTo('project-delete');
            //For logging out
            $user->givePermissionTo('partner');
        } elseif ($user->hasRole('Spider')) {
            $user->givePermissionTo('project-list');
            $user->givePermissionTo('project-create');
            //For logging out
            $user->givePermissionTo('partner');
        } elseif ($user->hasRole('Partner')) {
            $user->givePermissionTo('partner');
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function show(int $id)
    {
        $user = User::find($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit(int $id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRoles = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'roles', 'userRoles'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'roles' => 'required'
        ]);
        $input = $request->all();

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function invite_view(Project $project)
    {
        if ($user = Auth::user()) {
            if (!$user->hasRole(['Administrator']) && !$user->hasPermissionTo('project-' . $project->id . '-edit')) {
                abort(403);
            }
        }
        $organisations = Organisation::all();
        return view('projectadmin.invite', compact('project', 'organisations'));
    }

    public function process_invites(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'org' => 'required'
        ]);

        $validator->after(function ($validator) use ($request) {
            if (Invite::where('email', $request->input('email'))->exists()) {
                $validator->errors()->add('email', 'There exists an invite with this email!');
            }
        });
        if ($validator->fails()) {
            return redirect(route('invite_view', $request->input('project_id')))
                ->withErrors($validator)
                ->withInput();
        }
        do {
            $token = Str::random(20);
        } while (Invite::where('token', $token)->first());
        Invite::create([
            'token' => $token,
            'email' => $request->input('email'),
            'project_id' => $request->input('project_id'),
            'org_id' => $request->input('org')
        ]);

        $url = URL::temporarySignedRoute(

            'registration', now()->addMinutes(4320), ['token' => $token]
        );
        Mail::to($request->input('email'))->send(new PartnerInvite($url, $request->email));
        return redirect(session('links')[0]);
        //return redirect('/users')->with('success', 'The Invite has been sent successfully');
    }

    public function registration_view($token)
    {
        if ($invite = Invite::where('token', $token)->first())
            return view('auth.register', ['invite' => $invite]);
        else abort(403);
    }

    public function remove_invite(Invite $invite): RedirectResponse
    {
        $invite->delete();
        return back();
    }
}
