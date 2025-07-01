<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class TicketHistory extends Model
{
    use HasFactory;

    protected $table = 'Ticket_History'; // Explicitly define the table name

    protected $primaryKey = 'history_id'; // Explicitly define the primary key

    public $timestamps = false; // Indicates that the table does not have 'updated_at' column

    protected $fillable = [
        'ticket_id',
        'user_uuid',
        'action',
        'old_value',
        'new_value',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}