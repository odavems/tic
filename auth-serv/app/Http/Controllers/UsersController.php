<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function users(string $uuid)
    {
        // We already have the UUID from the route parameter no need to get it from request
        
        //Check if user is authenticated (assuming JWT or similar auth is being used)
        // if (!auth()->check()) {
        //     return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        // }
        
        // Find the user by UUID
        $user = User::where('uuid', $uuid)->first();
        
        // If user not found, return 404
        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        
        // Return user data as JSON response
        return response()->json($user);
    }
}
