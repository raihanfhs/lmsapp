<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

class StudentGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'grade',
        'passed',
        'attempt_datetime',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'grade' => 'decimal:2',
            'passed' => 'boolean',
            'attempt_datetime' => 'datetime',
        ];
    }

    /** Get the student (user) this grade belongs to. */
    public function student() // Changed from user() for clarity maybe? Or keep user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Get the course this grade belongs to. */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}