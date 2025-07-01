<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Site extends Model
{
    use HasFactory;

    protected $table = 'Sites'; // Explicitly define the table name

    protected $primaryKey = 'site_id'; // Explicitly define the primary key

    public $timestamps = true; // Indicates that the table has 'created_at' and 'updated_at' columns

    protected $fillable = [
        'customer_id',
        'site_name',
        'active',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'site_id');
    }
}