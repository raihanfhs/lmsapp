<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'issue_date',
        'certificate_path',
        'unique_code',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
        ];
    }

    /** Get the student (user) this certificate belongs to. */
    public function student() // Or keep user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Get the course this certificate is for. */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}