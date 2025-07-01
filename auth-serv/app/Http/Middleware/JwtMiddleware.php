<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Illuminate\Http\Response as HttpResponse;

// use Symfony\Component\HttpFoundation\Response;

// use Exception;
// use Firebase\JWT\ExpiredException;
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;


// class JwtMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */

//     // public function handle(Request $request, Closure $next): Response
//     // {
//     //     return $next($request);
//     // }

//     public function handle(Request $request, Closure $next): Response
//     {
//         $token = $request->header('Authorization');
//         if(!$token){
//             return response()->json(['error' => 'Token no encontrado']);
//             //return response()->json(['error' => 'Token no encontrado'], HttpResponse::HTTP_UNAUTHORIZED);
//         }

//         //si tengo el token entonces extraerlo y decodificarlo
//         try{
//             $token = str_replace('Bearer ', '', $token);
//             $decoded = JWT::decode($token, new Key(env('JWT_SECRET'),'HS256'));
//             return $next($request);
//         }catch(ExpiredException $e){
//             return response()->json(['error' => 'Token expirado']);
//             //return response()->json(['error' => 'Token expirado'], HttpResponse::HTTP_UNAUTHORIZED);
//         }catch(Exception $e){
//             return response()->json(['error' => 'Token Invalido: '.$e->getMessage()]);
//             //return response()->json(['error' => 'Token Invalido: '.$e->getMessage()], HttpResponse::HTTP_UNAUTHORIZED);
//         }
        
//     }

//}