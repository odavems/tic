<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Assignment extends Model
{
    use HasFactory;

    protected $table = 'Assignments'; // Explicitly define the table name

    protected $primaryKey = 'assignment_id'; // Explicitly define the primary key

    public $timestamps = false; // Indicates that the table does not have 'created_at' and 'updated_at' columns

    protected $fillable = [
        'ticket_id',
        'technician_uuid',
        'supervisor_uuid',
        'assigned_at',
        'due_date',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}