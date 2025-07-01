<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'Customers'; // Explicitly define the table name

    protected $primaryKey = 'customer_id'; // Explicitly define the primary key

    public $timestamps = true; // Indicates that the table has 'created_at' and 'updated_at' columns

    protected $fillable = [
        'customer_name',
        'active',
    ];

    // Relationships
    public function sites()
    {
        return $this->hasMany(Site::class, 'customer_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'customer_id');
    }
}