<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected function redirectTo(Request $request)
    {
        if(!$request->expectsJson()){
            abort(response()->json([
                'message' => 'Unauthorized'
            ],Response::HTTP_UNAUTHORIZED));
        }
    }
}
