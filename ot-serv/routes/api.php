<?php

use Dom\Mysql;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\OtController;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Contracts\Providers\JWT;

//use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware([JwtMiddleware::class])
    ->prefix('v1')
    ->group(function () {
        // Protected routes that require JWT authentication
        Route::get('/ots', [OtController::class, 'index']);
        Route::post('/ots', [OtController::class, 'store']);
        Route::get('/ots/{id}', [OtController::class, 'show']);
        Route::get('/ots/searchByName', [OtController::class,'searchByName']);
        Route::put('/ots/{id}', [OtController::class,'update']);
        Route::delete('/ots/{id}', [OtController::class,'destroy']);
    });


// prueba para insertar un ticket SIN TOKEN
Route::get('/test', function (Request $request) {
    try{
    
        //antes de probar, poblar primero las tablas:
        //1. customers
        //2. sites
        $newTicketId = DB::table('tickets')->insertGetId([

            'ticket_id' => null, // Auto-incremented by the database
            'title'  => 'Ticket Test 005',
            'description'  => 'Esta es una prueba de ticket 5',
            'status' => 'asignado',
            // 'status'  => 2,
            'worktype' => 1,
            'alarmtype' => 1,
            'priority' => 1,

            // 'worktype' => 'telecom',
            // 'alarmtype' => 'hardware',
            // 'priority' => 'medio',
            'inc_code' => 'INC0000003',
            'category' => 'Escalados',
            'customer_id' => 1,
            'site_id' => 1,
            'created_by_uuid' => 6,
            'assigned_to_uuid' => 1,
            'supervisor_uuid' => 6,
            'created_at' => now(), // Laravel will automatically manage timestamps
            //'updated_at' => now(),
            //'resolved_at' => date_timestamp_get(date_create()),
            //'closed_at' => date_timestamp_get(date_create()),

        ]);
        
        $createdByUserUuid = 6;

        $oldTicketId=3;
        $retrievedTicket = DB::table('tickets')->where('ticket_id', $oldTicketId)->first();

        return response()->json([
            'message' => 'Ticket creado exitosamente',
            'ticket' => $newTicketId,
            'created_by_uuid' => $createdByUserUuid,
            'retrieved_status' => $retrievedTicket ? $retrievedTicket->status : null, // Check if ticket was found // Check the retrieved status
        ]);
        //]$result->toArray());
        
    }catch (Exception $e){
        return response()->json([
            'message' => 'Error creating product',
            'error' => $e->getMessage()
        ],500);
    }    
        //return $request->user();
    }); 
    //->middleware('auth:api');


//RUTA PARA FEATURE TEST
//Route::apiResource('tasks', TaskController::class);
//Route::get('/tickets', [YourController::class, 'index']);