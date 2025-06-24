<?php

namespace App\Models;

use App\Models\StudentDetail; // <-- ADD THIS LINE
use App\Models\TeacherDetail;
use App\Models\Profile;
use App\Models\Course;
use App\Models\StudentGrade;
use App\Models\Certificate;
use App\Models\Division;
use App\Models\Skill;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Modify this line:
class User extends Authenticatable implements MustVerifyEmail // Add MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

   /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'division_id',
    ];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function studentDetail()
    {
        // A user has one StudentDetail record (or none)
        return $this->hasOne(StudentDetail::class);
    }

    /**
     * Get the teacher details associated with the user.
     * Defines a one-to-one relationship.
     */
    public function teacherDetail()
    {
        // A user has one TeacherDetail record (or none)
        return $this->hasOne(TeacherDetail::class);
    }
    public function teachingCourses()
    {
        // Links to Course model via the 'course_teacher' pivot table
        return $this->belongsToMany(Course::class, 'course_teacher');
    }
    // In app/Models/Course.php
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'user_id');
    }
    /**
     * The courses that the user (Student) is enrolled in.
     * Defines a many-to-many relationship.
     * (Keep this existing relationship)
     */
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id');
    }


    /**
     * Get the grades recorded for this user (Student).
     * Defines a one-to-many relationship.
     */
    public function studentGrades()
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Get the certificates earned by this user (Student).
     * Defines a one-to-many relationship.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
    
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'skill_user');
    }

    public function enrolledLearningPaths(): BelongsToMany
    {
        return $this->belongsToMany(LearningPath::class, 'learning_path_user');
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }

    
}

