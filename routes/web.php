<?php

use Illuminate\Support\Facades\Route;
// Cargando clases
use App\Http\Middleware\ApiAuthMiddleware;
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
Route::delete('/api/collaborator/delete/{id}', 'CollaboratorController@destroy')->middleware(ApiAuthMiddleware::class);
Route::get('/api/collaborator/detail/{id}', 'CollaboratorController@detail')->middleware(ApiAuthMiddleware::class);

Route::resource('/api/product', 'ProductController'); //CRUD
Route::resource('/api/client', 'ClientController'); //CRUD
Route::resource('/api/material', 'MaterialController');
Route::resource('/api/direction', 'DirectionController');
