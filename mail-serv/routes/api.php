<?php

use Dom\Mysql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\EmailController;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Contracts\Providers\JWT;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([JwtMiddleware::class])
    ->prefix('v1/emails')
    ->group(function () {
        // Protected routes that require JWT authentication
        Route::post('/', [EmailController::class, 'sendNotificacionCorreo'])->name('sendNotificacionCorreo');
    });
