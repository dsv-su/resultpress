<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Invite;
use App\Project_partner;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;


class ExternalRegisterController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {

        try {
            $provider_user = Socialite::driver($provider)->user();
        } catch (Exception $e) {
            return redirect()->route('partner-login');
        }

        //Check if user exist
        $findUser = User::where('email', $provider_user->email ?? '')->first();
        if($findUser)
        {
            Auth::login($findUser, true);
            return redirect()->route('project_home');
        }
        else
        {
            //Retrieve invite
            if($invite = Invite::where('email', $provider_user->email)->first())
            {
                //Create user
                $user = User::create([
                    'name' => $provider_user->name,
                    'email' => $provider_user->email,
                    'password' => Hash::make(Str::random(10)),
                ]);
                //Set user permissions
                $user->givePermissionTo('project-'.$invite->project_id.'-list'); //List project
                $user->givePermissionTo('project-'.$invite->project_id.'-update'); //Create project updates
                //Associate project with partner
                $project_partner = new Project_partner();
                $project_partner->project_id = $invite->project_id;
                $project_partner->partner_id = $user->id;
                $project_partner->save();
                $invite->delete();
                $user->assignRole('Partner');
                $user->givePermissionTo('partner');
                Auth::login($user, true);
                return redirect()->route('project_home');
            }

            else return redirect()->route('partner-login')->with('success', 'User does not exist in Resultpress. Please contact your Project Manager');
        }

    }
}
