<?php

namespace App\Models;

use App\Models\StudentDetail;
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

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'division_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELATIONSHIPS ---

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function studentDetail()
    {
        return $this->hasOne(StudentDetail::class);
    }

    public function teacherDetail()
    {
        return $this->hasOne(TeacherDetail::class);
    }

    public function teachingCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_teacher', 'user_id', 'course_id');
    }

    public function gradesGiven(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'teacher_id');
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'student_id');
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id')->withTimestamps();
    }

    public function studentGrades(): HasMany
    {
        // Secara eksplisit beritahu Laravel bahwa foreign key-nya adalah 'student_id'
        return $this->hasMany(StudentGrade::class, 'student_id');
    }

    public function certificates(): HasMany
    {
        // Secara eksplisit beritahu Laravel bahwa foreign key-nya adalah 'user_id'
        return $this->hasMany(Certificate::class, 'user_id');
    }

    
    
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'skill_user');
    }

    public function enrolledLearningPaths(): BelongsToMany
    {
        return $this->belongsToMany(LearningPath::class, 'learning_path_user');
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }
    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }

    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }
}