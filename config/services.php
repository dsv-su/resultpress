<?php
$file = base_path().'/systemconfig/resultpress.ini';
if (!file_exists($file)) {
    $file = base_path().'/systemconfig/resultpress.ini.example';
}
$system_config = parse_ini_file($file, true);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'github' => [
        'client_id' => $system_config['oauth2']['github_client_id'],
        'client_secret' => $system_config['oauth2']['github_client_secret'],
        'redirect' => $system_config['oauth2']['github_callback'],
    ],

    'facebook' => [
        'client_id' => $system_config['oauth2']['facebook_client_id'],
        'client_secret' => $system_config['oauth2']['facebook_client_secret'],
        'redirect' => $system_config['oauth2']['facebook_callback'],
    ],

    'linkedin' => [
        'client_id' => $system_config['oauth2']['linkedin_client_id'],
        'client_secret' => $system_config['oauth2']['linkedin_client_secret'],
        'redirect' => $system_config['oauth2']['linkedin_callback'],
    ],

    'google' => [
        'client_id' => $system_config['oauth2']['google_client_id'],
        'client_secret' => $system_config['oauth2']['google_client_secret'],
        'redirect' => $system_config['oauth2']['google_callback'],
    ],

];
