<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Jobs\SendNotificacionCorreo;
use App\Mail\NotificacionCorreo;
use Exception;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendNotificacionCorreo(Request $request)
    {

        //del array json
        try{
            $ticket = $request->input('ticket');
            $from = $request->input('from');
            $to = $request->input('to');
            // $ccadress = $request->input('ccAddress');
            $subject = $request->input('subject');
            $content = $request->input('content');

            //SIN JOB

            //NotificaccionCorreo esta en app/Mail/NotificacionCorreo.php
            //es un laravel Mailable class

            Mail::send(new NotificacionCorreo($ticket, $from, $to, $subject, $content));
            
            //MAIL TEST SIN array $ticket
            //Mail::send(new NotificacionCorreo($from, $to, $subject, $content));

            // SendNotificacionCorreo::dispatch(
            //     $ticket,
            //     $from,
            //     $to,
            //     $subject,
            //     $content
            // );
            return response()->json(['message' => 'Email sent successfully'], Response::HTTP_OK);

        }catch(Exception $ex){
            return response()->json(['error' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
