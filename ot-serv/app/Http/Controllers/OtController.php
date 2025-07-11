<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Exception;
use Illuminate\Http\Response;

//use App\Models\User;

//from JWT quickstart
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;


class OtController extends Controller
{

    protected $table;

    public function __construct()
    {
        // Set the table name for MySQL
        $this->table = 'tickets';
    }


    /**
     * Display a listing of the resource.
     */
//     public function index()
//    {
//         try {
//             // Using Laravel's query builder for MySQL
//             $tickets = DB::table($this->table)->get();
//             return response()->json($tickets, Response::HTTP_OK);
//         } catch (Exception $ex) {
//             return response()->json([
//                 'error' => $ex->getMessage()
//             ], Response::HTTP_INTERNAL_SERVER_ERROR);
//         }
//     }

    public function index()
    {
        try {
            $tickets_per_page = 5; // Define how many tickets to display per page
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page number
            $offset = ($page - 1) * $tickets_per_page;
    
            // Get total number of tickets
            $total_tickets = DB::table($this->table)->count();
            $total_pages = ceil($total_tickets / $tickets_per_page);
    
            // Using Laravel's query builder for MySQL with pagination
            $tickets = DB::table($this->table)
                ->orderBy('created_at', 'desc')  // Add this line to order by 'created_at' in descending order
                ->offset($offset)
                ->limit($tickets_per_page)
                ->get();
    
            return response()->json([
                'tickets' => $tickets,
                'total_pages' => $total_pages,
                'current_page' => $page
            ], Response::HTTP_OK);
    
        } catch (Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
