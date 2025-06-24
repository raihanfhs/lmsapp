<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import the User model to define the relationship
use App\Models\User;

class TeacherDetail extends Model
{
    use HasFactory; // Keep the factory trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Important for linking
        'employee_id_number',
        'qualification',
        'department',
    ];

    /**
     * Define the inverse one-to-one relationship with User.
     * Get the user that owns these teacher details.
     */
    public function user()
    {
        // TeacherDetail belongs to one User
        return $this->belongsTo(User::class);
    }
}