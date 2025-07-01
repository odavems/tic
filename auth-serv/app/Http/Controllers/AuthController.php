<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
//from JWT quickstart

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
//use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

//todos lo metodos de la clase AuthController requieren
//Authentification Bearer Token para funcionar

class AuthController extends Controller
{
    protected $table;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->table = 'users';
    }


    public function index()
    {
         try {
             // Using Laravel's query builder for MySQL
             $users = DB::table($this->table)->get();
             return response()->json($users, Response::HTTP_OK);
         } catch (Exception $ex) {
             return response()->json([
                 'error' => $ex->getMessage()
             ], Response::HTTP_INTERNAL_SERVER_ERROR);
         }
     }

    // ----LOGIN----

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            //return response()->json(['error' => 'Unauthorized'], 401);
            return response()->json(['error' => 'No autorizado']);
            //return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }

        //return response()->json(['message' => 'Login correcto'], Response::HTTP_OK);
        return $this->respondWithToken($token);
    }



    //-----REGISTER----

    //se altera la funcion para agregar el uuid
    public function register(Request $request)
    {
        $validar = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validar->fails()) {
            return response()->json($validar->errors()->toJson(), 400);
        }
  
        // Generate a UUID
        $uuid = Str::uuid();
        
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
            'uuid' => $uuid, // Store the UUID in the 'uuid' column
        ]);

        return response()->json([
            'message'=>'Guardado con Exito', 
            'user'=>$user
        ],Response::HTTP_CREATED
        );

    }


    //-----GET USER  ME-----

    public function me()
    {
    
        //return response()->json(auth()->user());
        return response()->json(Auth::user());

        //return redirect()->back();
      
    }


    //-----LOG OUT-----

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Salida correctamente']);
    }



    // ----REFRESH TOKEN----
    
    public function refresh()
    {
        //return $this->respondWithToken(auth()->refresh());
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }



    // ----GET USER BY UUID----
    // This method is used to get user details by UUID
    public function users(string $uuid)
    {
        // We already have the UUID from the route parameter

        //El siguiente codigo era para checkear la autorizacion o devolver no auntentificado

        //OJO: igual se necesita el token. Si no se adjunta se tiene un error de:
        //" The GET method is not supported for route api/v1/auth/login. Supported methods: POST."

        //Check if user is authenticated (assuming JWT or similar auth is being used)
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        
        // Find the user by UUID
        $user = User::where('uuid', $uuid)->first();
        
        // If user not found, return 404
        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        // Return user data as JSON response
        return response()->json($user);
        //return redirect()->back();
    }


    //MY USER
    protected function myuser()
    {
        $user = Auth::user();
        return response()->json($user);
    }



    // ----GET TOKEN----

    protected function respondWithToken($token)
    {
        // Get the uuid of the authenticated user
        $uuid = auth()->user()->uuid; 
        //$uuid = (Auth::user())->uuid;

        return response()->json([
            'access_token' => $token,
            'uuid' => $uuid,
            'token_type' => 'bearer',
            //'expires_in' => auth()->factory()->getTTL() * 60
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ], Response::HTTP_OK);
    }

};

