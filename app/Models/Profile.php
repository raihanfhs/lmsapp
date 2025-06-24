<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Add this

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Make sure user_id is fillable if you create profiles programmatically
        'avatar_path',
        'bio',
        'phone_number',
        'address_line_1', // <-- Add this
        'address_line_2', // <-- Add this
        'city',           // <-- Add this
        'state',          // <-- Add this
        'postal_code',    // <-- Add this
        'country',    
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
