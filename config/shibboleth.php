<?php
use Illuminate\Support\Str;

$file = base_path().'/systemconfig/resultpress.ini';
if (!file_exists($file)) {
    $file = base_path().'/systemconfig/resultpress.ini.example';
}
$system_config = parse_ini_file($file, true);

return [

    /*
    |--------------------------------------------------------------------------
    | Views / Endpoints
    |--------------------------------------------------------------------------
    |
    | Set your login page, or login routes, here. If you provide a view,
    | that will be rendered. Otherwise, it will redirect to a route.
    |
 */

    'idp_login' => $system_config['global']['login_route'],
    //idp_logout' => '/Shibboleth.sso/Logout',
    'authenticated' => '/shibboleth',


    /*
    |--------------------------------------------------------------------------
    | Emulate an IdP
    |--------------------------------------------------------------------------
    |
    | In case you do not have access to your Shibboleth environment on
    | homestead or your own Vagrant box, you can emulate a Shibboleth
    | environment with the help of Shibalike.
    |
    | The password is the same as the username.
    |
    | Do not use this in production for literally any reason.
    |
     */

    'emulate_idp' => env('EMULATE_IDP', false),
    'emulate_idp_users' => [
        'admin' => [
            'displayName' => 'Admin User',
            'mail' => 'admin@dsv.su.se',
            'givenName' => 'Admin ResultPress',
            'sn' => 'User',
            'eppn' => 'admin',
        ],
        'staff' => [
            'displayName' => 'DSV Developer',
            'mail' => 'staff@dsv.su.se',
            'givenName' => 'Staff',
            'sn' => 'User',
            'eppn' => 'staff',
        ],
        'user' => [
            'displayName' => 'User User',
            'mail' => 'user@dsv.su.se',
            'givenName' => 'User',
            'sn' => 'User',
            'eppn' => 'user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Variable Mapping
    |--------------------------------------------------------------------------
    |
    | Change these to the proper values for your IdP.
    |
     */

    'entitlement' => $system_config['global']['authorization'],

    'user' => [
        'name' => env('SHIBB_NAME', 'displayName'),
        'first_name' => env('SHIBB_FNAME', 'givenName'),
        'last_name' => env('SHIBB_LNAME', 'sn'),
        'email' => env('SHIBB_EMAIL', 'mail'),
        'emplid' => env('SHIBB_EMPLID', 'eppn'),
    ],

    //The user model field (from the user array above) that should be used for authentication
    'user_authentication_field' => 'email',

    /*
    |--------------------------------------------------------------------------
    | User Creation and Groups Settings
    |--------------------------------------------------------------------------
    |
    | Allows you to change if / how new users are added
    |
     */

    'add_new_users' => true, // Should new users be added automatically if they do not exist?

    /*
    |--------------------------------------------------------------------------
    | JWT Auth
    |--------------------------------------------------------------------------
    |
    | JWTs are for the front end to know it's logged in
    |
    | https://github.com/tymondesigns/jwt-auth
    | https://github.com/StudentAffairsUWM/Laravel-Shibboleth-Service-Provider/issues/24
    |
     */

    'jwtauth' => env('JWTAUTH', false),
];
