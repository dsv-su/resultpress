<?php

namespace App\Http\Controllers;

use App\Invite;
use App\Notifications\InviteNotification;
use Illuminate\Http\Request;
use App\User;
use App\Project;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use DB;
use Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('registration_view');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        if($user->hasRole('Administrator'))
        {
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
        }
        elseif ($user->hasRole('Program administrator'))
        {
            $user->givePermissionTo('project-list');
            $user->givePermissionTo('project-update');
            $user->givePermissionTo('project-create');
            $user->givePermissionTo('project-edit');
            $user->givePermissionTo('project-delete');
            //For logging out
            $user->givePermissionTo('partner');
        }
        elseif ($user->hasRole('Spider'))
        {
            $user->givePermissionTo('project-list');
            $user->givePermissionTo('project-create');
            //For logging out
            $user->givePermissionTo('partner');
        }
        elseif ($user->hasRole('Partner'))
        {
            $user->givePermissionTo('partner');
        }

        return redirect()->route('users.index')
            ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRoles = $user->roles->pluck('name','name')->all();

        return view('users.edit',compact('user','roles','userRoles'));

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
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'roles' => 'required'
        ]);
        $input = $request->all();

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')
            ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success','User deleted successfully');
    }

    public function invite_view(Project $project)
    {
        return view('projectadmin.invite', compact('project'));
    }

    public function process_invites(Request $request)
    {
        //dd($request->input('project_id'));
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email'
        ]);
        $validator->after(function ($validator) use ($request) {
            if (Invite::where('email', $request->input('email'))->exists()) {
                $validator->errors()->add('email', 'There exists an invite with this email!');
            }
        });
        if ($validator->fails()) {
            return redirect(route('invite_view'))
                ->withErrors($validator)
                ->withInput();    }
        do {
            $token = Str::random(20);
        } while (Invite::where('token', $token)->first());
        Invite::create([
        'token' => $token,
        'email' => $request->input('email'),
        'project_id' => $request->input('project_id')
        ]);

        $url = URL::temporarySignedRoute(

            'registration', now()->addMinutes(480), ['token' => $token]
        );
        Notification::route('mail', $request->input('email'))->notify(new InviteNotification($url));
        return redirect('/users')->with('success', 'The Invite has been sent successfully');
    }

    public function registration_view($token)
    {
        if($invite = Invite::where('token', $token)->first())
        return view('auth.register',['invite' => $invite]);
        else abort(401);
    }
}
