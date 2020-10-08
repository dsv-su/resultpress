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

Route::middleware('entitlements')->group(function () {
    Route::get('/', 'ProjectController@index')->name('home');
    Route::get('/project/create', 'ProjectController@edit')->name('project_create');
    Route::post('/project', 'ProjectController@update')->name('update');
    Route::get('/project/update/{project_update}', 'ProjectUpdateController@show')->name('projectupdate_show');
    Route::get('/project/update/{project_update}/edit', 'ProjectUpdateController@edit')->name('projectupdate_edit');
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
    Route::post('/store_file', 'FileController@store')->name('store_file');

    //Logging
    Route::get('/logs', 'LogsController@index')->name('logs');
    //Admin User/Role management
    Route::get('/admin', 'AdminController@index')->name('admin');
});
Route::group(['middleware' => ['auth']], function() {
    Route::resource('projectadmin','ProjectAdminController');
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
});
//Test routes
Route::get('/server', 'TestController@server');
