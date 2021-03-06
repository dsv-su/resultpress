<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Invite;
use App\ProjectPartner;
use App\Providers\RouteServiceProvider;
use App\User;
use App\UserOrganisation;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        //Retrieve invite
        $invite = Invite::where('token', $data['token'])->first();
        //Check if user exist
        $findUser = User::where('email', $data['email'])->first();
         if($findUser)
         {
            return redirect()->route('partner-login')->with('success','User already exists. Please inform your Project Manager');
         }
         else
         {
             //Create user
             $user = User::create([
                 'name' => $data['name'],
                 'email' => $data['email'],
                 'password' => Hash::make($data['password']),
             ]);

             //Associate user with organisation
             $org = UserOrganisation::create([
                 'user_id' => $user->id,
                 'organisation_id' => $invite->org_id,
             ]);

             //Set user permissions
             $user->givePermissionTo('project-'.$invite->project_id.'-list'); //List project
             $user->givePermissionTo('project-'.$invite->project_id.'-update'); //Create project updates
             //Associate project with partner
             $project_partner = new ProjectPartner();
             $project_partner->project_id = $invite->project_id;
             $project_partner->partner_id = $user->id;
             $project_partner->save();
             $invite->delete();
             $user->assignRole('Partner');
             return $user->givePermissionTo('partner');

         }
    }


}
