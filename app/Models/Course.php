<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// Import necessary models for relationships
use App\Models\User;
use App\Models\CourseMaterial;
use App\Models\OnlineMeeting;
use App\Models\StudentGrade;
use App\Models\Certificate;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Course extends Model
{
    use HasFactory;

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
    public function teachers()
    {
        // Links to User model via the 'course_teacher' pivot table
        return $this->belongsToMany(User::class, 'course_teacher');
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
    public function onlineMeetings()
    {
        return $this->hasMany(OnlineMeeting::class)->orderBy('meeting_datetime', 'asc');
    }

    /**
     * Get the grades recorded for students in this course.
     * Defines a one-to-many relationship.
     */
    public function studentGrades()
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

    public function studentGrade(User $student)
    {
        return $this->studentGrades()->where('student_id', $student->id)->first();
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

}