<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\CourseMaterial;
use App\Models\Meeting;
use App\Models\StudentGrade;
use App\Models\Certificate;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Course extends Model
{
    use HasFactory, SoftDeletes;
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Keep existing relevant fields
        'title',
        'description',
        'course_code',
        // Add new fields (user_id removed)
        'duration_months',
        'final_exam_date',
        'passing_grade',
        'certificate_template_path',
        'status',
    ];

    /**
     * The attributes that should be cast.
     * Added casting for new date/datetime fields.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'final_exam_date' => 'datetime',
        ];
    }


    // --- RELATIONSHIPS ---

    /**
     * Get the teachers assigned to teach this course.
     * Defines a many-to-many relationship.
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'user_id');
    }

    /**
     * Get the students enrolled in this course.
     * Defines a many-to-many relationship.
     * (This relationship likely already existed)
     */
    public function enrolledStudents()
    {
        // Links to User model via the 'enrollments' pivot table
        return $this->belongsToMany(User::class, 'enrollments');
    }

    /**
     * Get the materials associated with the course.
     * Defines a one-to-many relationship.
     * (We added this previously)
     */
    public function materials()
    {
        // A course has many materials, order by 'order' column
        return $this->hasMany(CourseMaterial::class)->orderBy('order', 'asc');
    }

    /**
     * Get the online meetings scheduled for this course.
     * Defines a one-to-many relationship.
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class)->orderBy('meeting_datetime', 'asc');
    }

    public function onlineMeetings(): HasMany
    {
        return $this->meetings(); // Redirects to the correct relationship
    }

    /**
     * Get the grades recorded for students in this course.
     * Defines a one-to-many relationship.
     */
    public function grades()
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * Get the certificates issued for this course.
     * Defines a one-to-many relationship.
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }


    public function studentGrade(User $student)
    {
        return $this->grades()->where('student_id', $student->id)->first();
    }

    // Method pembantu untuk mencari sertifikat seorang student di kursus ini
    public function studentCertificate(User $student)
    {
        return $this->certificates()->where('student_id', $student->id)->first();
    }


    public function prerequisites()
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course_id', // Foreign key on pivot table for this course
            'prerequisite_id' // Foreign key on pivot table for the prerequisite course
        );
    }
    public function prerequisiteFor()
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'prerequisite_id', // Foreign key on pivot table for this course
            'course_id' // Foreign key on pivot table for the course that requires this one
        );
    }

    public function learningPaths(): BelongsToMany
    {
        return $this->belongsToMany(LearningPath::class, 'course_learning_path');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order');
    }
    
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withTimestamps(); // Optional: if you want to access created_at/updated_at on the enrollment record
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class);
    }
}