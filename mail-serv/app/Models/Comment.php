<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'Comments'; // Explicitly define the table name

    protected $primaryKey = 'comment_id'; // Explicitly define the primary key

    public $timestamps = true; // Indicates that the table has 'created_at' and 'updated_at' columns

    protected $fillable = [
        'ticket_id',
        'user_uuid',
        'content',
    ];

    // Relationships
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}