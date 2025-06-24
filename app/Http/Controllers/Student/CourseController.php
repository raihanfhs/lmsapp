<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Course;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses the student is enrolled in.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Get enrolled courses for the currently authenticated student
        $courses = Auth::user()->enrolledCourses()->latest()->paginate(10); // Paginate results

        // Return the view to display the enrolled courses
        // View file: resources/views/student/courses/index.blade.php (created in Phase 7)
        return view('student.courses.index', compact('courses'));
    }

    /**
 * Display the specified course and its materials for an enrolled student.
 *
 * @param Course $course Automatically injected by route-model binding
 * @return View|RedirectResponse
 */
public function show(Course $course): View|RedirectResponse
{
    $user = Auth::user();

    // Authorization Check 1: Is the user enrolled in this course?
    // Check if the course ID exists in the collection of courses the user is enrolled in.
    if (!$user->enrolledCourses()->where('course_id', $course->id)->exists()) {
         abort(403, 'You are not enrolled in this course.');
    }

    // Fetch the course (already injected) and eager load its materials, ordered correctly.
    // We load ALL materials and will handle hierarchy display in the view for simplicity first.
    $course->load([
        'materials' => function ($query) { /* ... */ },
        'onlineMeetings' => function ($query) { /* ... */ },
        'teachers' // <-- CRUCIAL: This loads the teachers
    ]);
    
    $course->load(['quizzes', 'onlineMeetings', 'materials']);

    $student = Auth::user();
    
    $student->load(['studentGrades' => fn($q) => $q->where('course_id', $course->id),
                    'certificates' => fn($q) => $q->where('course_id', $course->id)]);

     return view('student.courses.show', compact('course', 'student')); 
}

/**
 * Display a listing of all available courses for students to browse and enroll.
 *
 * @return \Illuminate\View\View
 */
public function browseCourses(): View
{
    // Fetch all courses. Later, you might add a filter for "published" or "active" courses.
    // Eager load teachers to display who is teaching the course.
    $courses = Course::with('teachers')->latest()->paginate(12); // Show 12 courses per page

    // Return the view, passing the courses data to it
    // View file: resources/views/student/courses/browse.blade.php (we create this next)
    return view('student.courses.browse', compact('courses'));
}
/**
 * Handle a student's request to enroll in a course, checking for prerequisites.
 *
 * @param Request $request
 * @param Course $course
 * @return RedirectResponse
 */
public function enroll(Request $request, Course $course): RedirectResponse
{
    $student = Auth::user();

    // --- Prerequisite Check ---
    $prerequisites = $course->prerequisites; // Get the collection of prerequisite courses

    if ($prerequisites->isNotEmpty()) { // Check if this course has any prerequisites
        // Get the IDs of all courses the student has passed
        // We check the student_grades table where passed = true
        $passedCourseIds = $student->studentGrades()->where('passed', true)->pluck('course_id')->toArray();

        foreach ($prerequisites as $prerequisite) {
            // Check if the student has passed EACH prerequisite course
            if (!in_array($prerequisite->id, $passedCourseIds)) {
                // If any prerequisite is not found in the passed list, redirect with an error
                return redirect()->route('student.courses.browse')
                                 ->with('error', "You cannot enroll. You must first complete the prerequisite course: '{$prerequisite->title}'.");
            }
        }
    }
    // --- End Prerequisite Check ---


    // --- Existing Logic: Check if already enrolled ---
    if ($student->enrolledCourses()->where('course_id', $course->id)->exists()) {
        return redirect()->route('student.courses.browse')
                         ->with('error', "You are already enrolled in '{$course->title}'.");
    }

    // --- Existing Logic: Create the enrollment record ---
    $student->enrolledCourses()->attach($course->id);

    // --- Existing Logic: Redirect back with a success message ---
    return redirect()->route('student.courses.index') // Redirect to their enrolled list after success
                     ->with('success', "Successfully enrolled in '{$course->title}'!");
}
}