<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import the User model to define the relationship
use App\Models\User;

class StudentDetail extends Model
{
    use HasFactory; // Keep the factory trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', // Important for linking
        'student_id_number',
        'enrollment_date',
        'major',
    ];

    /**
     * Define the inverse one-to-one relationship with User.
     * Get the user that owns these student details.
     */
    public function user()
    {
        // StudentDetail belongs to one User
        return $this->belongsTo(User::class);
    }
}