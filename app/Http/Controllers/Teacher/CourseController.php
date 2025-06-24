<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course; // Import the Course model
use App\Models\User;
use App\Models\StudentGrade;
use App\Models\Certificate;
use Illuminate\Http\Request; // Using basic Request for now
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CourseController extends Controller // <-- CHANGE THIS LINE
{
    /**
     * Display a listing of the teacher's courses.
     */

    public function gradeStudentForm(Course $course, User $user): View|RedirectResponse // Changed $student to $user
    {
        // Authorization Check 1: Teacher assigned to course
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }

        // Authorization Check 2: Student IS ENROLLED in this course
        // Now use the $user variable that matches the route parameter
        if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            abort(404, 'Student not enrolled in this course.');
        }

        $existingGrade = StudentGrade::where('user_id', $user->id) // Use $user->id
                                    ->where('course_id', $course->id)
                                    ->first();

        // Pass $user (which is the student) to the view. You might want to rename it in compact for clarity in the view.
        return view('teacher.courses.grade_student', [
            'course' => $course,
            'student' => $user, // Pass the bound $user model as 'student' to the view
            'existingGrade' => $existingGrade
        ]);
    }
    /**
     * Store or update the grade for a student in a specific course.
     */
    public function storeStudentGrade(Request $request, Course $course, User $user): RedirectResponse // CHANGED $student to $user
    {
        // Authorization checks (use $user variable)
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }
        if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) { // Use $user
            abort(404, 'Student not enrolled in this course.');
        }

        $validated = $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
        ]);

        $gradeValue = (float)$validated['grade'];
        $passed = ($course->passing_grade && $gradeValue >= $course->passing_grade);

        StudentGrade::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id], // Use $user->id
            ['grade' => $gradeValue, 'passed' => $passed, 'attempt_datetime' => now()]
        );

        $message = "Grade for {$user->name} in '{$course->title}' saved successfully."; // Use $user->name

        if ($passed && !$user->certificates()->where('course_id', $course->id)->exists()) { // Use $user
            Certificate::create([
                'user_id' => $user->id, // Use $user->id
                'course_id' => $course->id,
                'issue_date' => now(),
                'unique_code' => Str::random(10) . '-' . $user->id . '-' . $course->id,
            ]);
            $message .= " Certificate issued.";
        }

        return redirect()->route('teacher.courses.show', $course->id)->with('success', $message);
    }
    public function index(): View
    {
        // Get courses created ONLY by the currently authenticated teacher
        $courses = Auth::user()->teachingCourses()->latest()->paginate(10);// Paginate results

        // View file: resources/views/teacher/courses/index.blade.php
        return view('teacher.courses.index', compact('courses'));
    }


    public function show(Course $course): View|RedirectResponse
    {
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }

        // Eager load relationships for the course
        $course->load(['students', 'sections.materials']);

        // Now, for each enrolled student, eager load their grades and certificates for THIS course
        // This approach is more explicit and often more reliable for nested conditions.
        $course->enrolledStudents->each(function ($student) use ($course) {
            $student->load([
                'studentGrades' => fn($query) => $query->where('course_id', $course->id),
                'certificates' => fn($query) => $query->where('course_id', $course->id)
            ]);
        });

        // The $course object now has enrolledStudents, and each student
        // has their relevant studentGrades and certificates loaded.
        // The view will use $course->enrolledStudents.

        return view('teacher.courses.show', compact('course'));
    }
    
}