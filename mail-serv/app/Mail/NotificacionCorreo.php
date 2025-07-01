<?php

//All of this NotificacionCorreo.php is a Laravel Mailable class.
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificacionCorreo extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket; // This will now always be an array
    public $fromAddress;
    public $toAddress;
    public $ccAddress;
    public $subject;
    public $contentBody;

    /**
     * Create a new message instance.
     */
    public function __construct($ticket,$from,$to,$sub,$content)
    {
        //la informacion del constructor vienen del JSON

        //$this->ticket = $ticket; 
        
        // Convertir en array el array-json ticket
        $this->ticket = is_array($ticket) ? $ticket : [$ticket];
        
        $this->fromAddress = $from;
        $this->toAddress  = $to;
        $this->subject = $sub;
        $this->contentBody = $content;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        //informacion de metadata del correo

        return new Envelope(
            //subject: 'ticket Notificacion',

            //Subject: del array json de prueba
            //Content: del array json de prueba
            subject: $this->subject,
            
            //from: $this->fromAddress => ['Nuevo Ticket'],
            
            //sin array 
            from: $this->fromAddress,
            to: $this->toAddress,
            //cc: $this->ccAddress,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        //el contenido del correo usa la plantilla ticket-notificacion.blade.php

        return new Content(
            //view: 'view.name',
            view: 'emails.ticket-notificacion',
            with:[
                'ticket' => $this->ticket,
                'fromAddress' => $this->fromAddress,
                'toAddress' => $this->toAddress,
                //'ccAddress' => $this->ccAddress,
                'subject' => $this->subject,
                'contentBody' => $this->contentBody,
            ]
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
