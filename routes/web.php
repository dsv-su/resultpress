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
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/projects', 'ProjectController@index')->name('project_home');
    Route::get('/shibboleth', 'ProjectController@shibboleth')->name('shibboleth');
    Route::get('/project/create', 'ProjectController@edit')->name('project_create');
    Route::post('/project', 'ProjectController@update')->name('update');
    Route::get('/au/{activity}/{activity_update}', 'ProjectUpdateController@showActivityUpdateForm')->name('au_update_form');
    Route::get('/a/{activity}/{index}', 'ProjectUpdateController@showActivityCreateForm')->name('au_create_form');
    //Route::get('/au/{activity_update}/show', 'ProjectUpdateController@showActivityUpdate')->name('au_show');
    Route::get('/project/update/{project_update}', 'ProjectUpdateController@show')->name('projectupdate_show');
    Route::get('/project/update/{project_update}/edit', 'ProjectUpdateController@edit')->name('projectupdate_edit');
    Route::get('/project/update/{project_update}/delete', 'ProjectUpdateController@destroy')->name('projectupdate_delete');
    Route::get('/project/update/{project_update}/review', 'ProjectUpdateController@review')->name('projectupdate_review');
    Route::put('/project/update/{project_update}/review', 'ProjectUpdateController@update')->name('projectupdate_update');
    Route::get('/project/{project}', 'ProjectController@show')->name('project_show');
    Route::get('/project/{project}/delete', 'ProjectController@destroy')->name('project_delete');
    Route::get('/project/{project}/archive', 'ProjectController@archive')->name('project_archive');
    Route::get('/project/{project}/unarchive', 'ProjectController@unarchive')->name('project_unarchive');
    Route::get('/project/{project}/edit', 'ProjectController@edit')->name('project_edit');
    Route::get('/project/{project}/history', 'ProjectController@history')->name('project_history');
    Route::get('/project/{project}/update', 'ProjectController@write_update')->name('project_write_update');
    Route::get('/project/{project}/updates', 'ProjectUpdateController@index')->name('projectupdate_index');
    Route::put('/project/{project}/update', 'ProjectController@save_update')->name('project_save_update');
    Route::get('/project/{project}/accept', 'ProjectController@accept')->name('project_accept');
    Route::get('/project/{project}/reject', 'ProjectController@reject')->name('project_reject');
    Route::put('/project/{project}', 'ProjectController@update')->name('project_update');
    Route::get('/project/{project}/archive', 'ProjectController@archive')->name('project_archive');
    Route::get('/project/{project}/unarchive', 'ProjectController@unarchive')->name('project_unarchive');
    Route::get('/outcome_update/{outcome}/{outcome_update}', 'ProjectUpdateController@showOutcomeUpdateForm')->name('outcome_update_form');
    Route::get('/reminder/{reminder}', 'ProjectUpdateController@showReminderCreateForm')->name('reminder_create_form');
    Route::post('/store_file', 'FileController@store')->name('store_file');
    Route::post('/complete_activity', 'ProjectController@completeActivity')->name('complete_activity');
    //Program Areas
    Route::get('/programareas', 'ProgramAreaController@index')->name('programareas');
    Route::get('/programareas/create', 'ProgramAreaController@create')->name('areas.create');
    Route::post('/programareas/store', 'ProgramAreaController@store')->name('areas.store');
    Route::delete('/programareas/destroy/{id}', 'ProgramAreaController@destroy')->name('areas.destroy');
    Route::put('/programareas/unarchive/{id}', 'ProgramAreaController@unarchive')->name('areas.unarchive');
    Route::get('/programareas/{id}', 'ProgramAreaController@show')->name('programarea_show');
    Route::get('/programareas/{id}/edit', 'ProgramAreaController@edit')->name('programarea_edit');
    Route::post('/programareas/{id}/update', 'ProgramAreaController@update')->name('programarea_update');
    //Logging
    Route::get('/logs', 'LogsController@index')->name('logs');
    //Admin User/Role management
    Route::get('/admin', 'AdminController@index')->name('admin');
    //Administrator routes
    Route::resource('settings', SettingsController::class);
    Route::resource('projectadmin','ProjectAdminController');
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
    Route::resource('organisation', 'OrganisationController');

    // Upload logo
    Route::post('/systemUploadLogo', 'SettingsController@updateLogo')->name('systemUploadLogo');

    Route::post('/user/invite', 'UserController@process_invites')->name('process_invite');
    Route::get('/user/invite/{project}', 'UserController@invite_view')->name('invite_view');
    Route::get('/user/remove_invite/{invite}', 'UserController@remove_invite')->name('invite_remove');

    //Search 
    Route::get('/search/{q?}', 'SearchController@search')->name('search');
    Route::post('/search', 'SearchController@filterSearch')->name('filter_search');
    Route::get('/find', 'SearchController@find')->name('find');

    //Profile
    Route::get('/home', 'HomeController@index')->name('profile');
    Route::post('/profile/{id}', 'HomeController@store')->name('profile_store');
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

