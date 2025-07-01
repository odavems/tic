<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'Tickets'; // Explicitly define the table name

    protected $primaryKey = 'ticket_id'; // Explicitly define the primary key

    public $timestamps = true; // Indicates that the table has 'created_at' and 'updated_at' columns

    protected $fillable = [
        'title',
        'description',
        'status',
        'worktype',
        'alarmtype',
        'priority',
        //'inc_code',
        //'category',
        'customer_id',
        'site_id',
        'created_by_uuid',
        'assigned_to_uuid',
        'supervisor_uuid',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'status' => 'string',
        'worktype' => 'string',
        'alarmtype' => 'string',
        'priority' => 'string',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'ticket_id');
    }

    public function ticketHistories()
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'ticket_id');
    }
}