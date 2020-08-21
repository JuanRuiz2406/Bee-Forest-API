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

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/collaborator/register', 'CollaboratorController@register');
Route::post('/api/collaborator/login', 'CollaboratorController@login');
Route::put('/api/collaborator/update', 'CollaboratorController@update');