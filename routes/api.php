<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Models\Shipping;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('collaborator/register', [CollaboratorController::class, 'register']);
Route::post('collaborator/login', [CollaboratorController::class, 'login']);
Route::put('collaborator/update',  [CollaboratorController::class, 'update']);
Route::delete('collaborator/delete/{id}',  [CollaboratorController::class, 'destroy'])->middleware(ApiAuthMiddleware::class);
Route::get('collaborator/detail/{id}', [CollaboratorController::class, 'detail'])->middleware(ApiAuthMiddleware::class);

Route::resource('product', ProductController::class); //CRUD
Route::resource('client', ClientController::class); //CRUD
Route::resource('provider', ProviderController::class); //CRUD
Route::resource('material', MaterialController::class);
Route::resource('category', CategoryController::class);
Route::resource('shipping', ShippingController::class);
Route::resource('order', OrderController::class);

//Rutas de Direcicones
Route::resource('direction', DirectionController::class);
Route::get('direction/get-direction-client/{id}', [DirectionController::class, 'indexByClient']);
