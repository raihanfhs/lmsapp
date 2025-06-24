<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\User;

class OnlineMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'title',
        'description',
        'meeting_datetime',
        'meeting_link',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'meeting_datetime' => 'datetime',
        ];
    }

    /** Get the course this meeting belongs to. */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /** Get the teacher associated with this meeting (optional). */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}