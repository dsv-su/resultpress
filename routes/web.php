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


//Route::middleware('entitlements')->group(function () {

Route::get('/', 'ProjectController@index')->name('home');
Route::get('/project/create', 'ProjectController@edit')->name('project_create');
Route::post('/project', 'ProjectController@update')->name('update');
Route::get('/project/{project}', 'ProjectController@show')->name('project_detail');
Route::get('/project/{project}/edit', 'ProjectController@edit')->name('project_edit');
Route::put('/project/{project}', 'ProjectController@update')->name('project_update');
Route::get('/project/{project}/delete', 'ProjectController@destroy')->name('project_delete');

//});
