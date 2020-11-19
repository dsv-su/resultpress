<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Services\AuthHandler;

if (class_exists(AuthHandler::class))
    $login = app()->make('SystemService')->authorize()->global->login_route;
//Endpoint
Route::get($login, 'SystemController@login')->name('login');

Route::middleware('auth', 'entitlements')->group(function () {
    Route::get('/', 'ProjectController@index')->name('project_home');
    Route::get('/shibboleth', 'ProjectController@shibboleth')->name('shibboleth');
    Route::get('/project/create', 'ProjectController@edit')->name('project_create');
    Route::post('/project', 'ProjectController@update')->name('update');
    Route::get('/project/update/{project_update}', 'ProjectUpdateController@show')->name('projectupdate_show');
    Route::get('/project/update/{project_update}/edit', 'ProjectUpdateController@edit')->name('projectupdate_edit');
    Route::get('/project/update/{project_update}/delete', 'ProjectUpdateController@destroy')->name('projectupdate_delete');
    Route::get('/project/update/{project_update}/review', 'ProjectUpdateController@review')->name('projectupdate_review');
    Route::put('/project/update/{project_update}/review', 'ProjectUpdateController@update')->name('projectupdate_update');
    Route::get('/project/{project}', 'ProjectController@show')->name('project_show');
    Route::get('/project/{project}/delete', 'ProjectController@destroy')->name('project_delete');
    Route::get('/project/{project}/edit', 'ProjectController@edit')->name('project_edit');
    Route::get('/project/{project}/update', 'ProjectController@write_update')->name('project_write_update');
    Route::get('/project/{project}/updates', 'ProjectUpdateController@index')->name('projectupdate_index');
    Route::put('/project/{project}/update', 'ProjectController@save_update')->name('project_save_update');
    Route::put('/project/{project}', 'ProjectController@update')->name('project_update');
    Route::get('/project/{project}/delete', 'ProjectController@destroy')->name('project_delete');
    Route::put('/outcome_update/{outcome}', 'OutcomeController@update')->name('outcome_update');
    Route::post('/store_file', 'FileController@store')->name('store_file');
    //Program Areas
    Route::get('/programareas', 'ProgramAreaController@index')->name('programareas');
    Route::get('/programareas/{id}', 'ProgramAreaController@show')->name('programarea_show');
    Route::get('/programareas/{id}/edit', 'ProgramAreaController@edit')->name('programarea_edit');
    Route::post('/programareas/{id}/update', 'ProgramAreaController@update')->name('programarea_update');
    //Logging
    Route::get('/logs', 'LogsController@index')->name('logs');
    //Admin User/Role management
    Route::get('/admin', 'AdminController@index')->name('admin');
    //Administrator routes
    Route::resource('projectadmin','ProjectAdminController');
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
    Route::post('/user/invite', 'UserController@process_invites')->name('process_invite');
    Route::get('/user/invite/{project}', 'UserController@invite_view')->name('invite_view');
});

// Local registration route
Route::get('/registration/{token}', 'UserController@registration_view')->name('registration');
Route::POST('register', 'Auth\RegisterController@register')->name('accept');

//Local login/logout route
Route::get('partner-login', 'Auth\LoginController@showLoginForm')->name('partner-login');
Route::post('partner-login', 'Auth\LoginController@login');
Route::post('partner-logout', 'Auth\LoginController@logout')->name('partner-logout');

//Local password reset
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

//External register and login routes for oauthlogin
Route::get('partner-login/{provider}', 'Auth\ExternalRegisterController@redirectToProvider');
Route::get('/partner-login/{provider}/callback', 'Auth\ExternalRegisterController@handleProviderCallback');

//Testroute(to be removed)
Route::get('/server', 'TestController@server');
Route::get('/home', 'HomeController@index')->name('home');
