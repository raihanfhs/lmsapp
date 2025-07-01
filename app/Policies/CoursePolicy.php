<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can update the model.
     * We will use this check to see if a teacher can manage meetings for a course.
     */
    public function update(User $user, Course $course): bool
    {
        // This checks if the user is in the list of teachers assigned to this specific course.
        // This logic already exists in your Teacher/CourseController and is very efficient.
        return $user->teachingCourses()->where('course_id', $course->id)->exists();
    }
}