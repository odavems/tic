<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Http\Controllers\UsersController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

//from JWT quickstart
Route::group([
    //'middleware' => 'api',
    //'middleware' => 'jwt.auth',
    'prefix' => 'v1/auth'

], function ($router) {
    Route::post('login', [AuthController::class,'login'])->name('login');
    Route::post('register', [AuthController::class,'register']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::get('me', [AuthController::class,'me']);

    Route::get('myuser', [AuthController::class,'myuser']);
    Route::get('/users', [AuthController::class, 'index']);
    Route::get('users/{uuid}', [AuthController::class, 'users']);
});

//esta ruta  devuelve el json del user sin token
//porque esta desactivada la autenticacion en la funcion
Route::get('/users/{uuid}', [UsersController::class, 'users']);
