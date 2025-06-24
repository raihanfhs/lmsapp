<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;              
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Admin sees all courses, eager load the assigned teachers relationship
        // Use paginate for large number of courses
        $courses = Course::with('teachers')->latest()->paginate(15);
        return view('pengelola.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $allCourses = Course::orderBy('title')->get();

        return view('pengelola.courses.create', compact('allCourses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse // Use specific Form Request later
    {
    // TODO: Move validation to StoreCourseRequest later
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50|unique:courses,course_code',
            'description' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'final_exam_date' => 'nullable|date',
            'passing_grade' => 'nullable|integer|min:0|max:100',
            'certificate_template_path' => 'nullable|string|max:255',
            'prerequisites' => 'nullable|array', // <-- ADD VALIDATION FOR PREREQUISITES
            'prerequisites.*' => 'integer|exists:courses,id' // <-- Ensure each ID exists
        ]);

        // Create the course with data that belongs to the courses table
        // We separate prerequisites because they belong in the pivot table
        $course = Course::create([
            'title' => $validated['title'],
            'course_code' => $validated['course_code'] ?? null,
            'description' => $validated['description'] ?? null,
            'duration_months' => $validated['duration_months'] ?? null,
            'final_exam_date' => $validated['final_exam_date'] ?? null,
            'passing_grade' => $validated['passing_grade'] ?? null,
            'certificate_template_path' => $validated['certificate_template_path'] ?? null,
        ]);

        // Attach prerequisites if they were selected in the form
        if ($request->has('prerequisites')) {
            $course->prerequisites()->attach($validated['prerequisites']);
        }

        return redirect()->route('pengelola.courses.index')
                        ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        // Fetch all courses EXCEPT the current one
        $allCourses = Course::where('id', '!=', $course->id)->orderBy('title')->get();

        // Get the IDs of the courses that are currently prerequisites for this course
        $prerequisiteIds = $course->prerequisites()->pluck('courses.id')->toArray();

        return view('pengelola.courses.edit', compact('course', 'allCourses', 'prerequisiteIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required','string','max:255', \Illuminate\Validation\Rule::unique('courses')->ignore($course->id)],
            'course_code' => ['nullable','string','max:50', \Illuminate\Validation\Rule::unique('courses')->ignore($course->id)],
            'description' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'final_exam_date' => 'nullable|date',
            'passing_grade' => 'nullable|integer|min:0|max:100',
            'certificate_template_path' => 'nullable|string|max:255',
            'prerequisites' => 'nullable|array', // <-- ADD VALIDATION
            'prerequisites.*' => 'integer|exists:courses,id' // <-- ADD VALIDATION
        ]);

        // Separate prerequisites from main course data
        $prerequisiteIds = $validated['prerequisites'] ?? [];
        unset($validated['prerequisites']); // Remove from validated array before updating the course model

        // Update the main course record
        $course->update($validated);

        // Sync the prerequisites
        // sync() will add new prerequisites and remove any that were unchecked
        $course->prerequisites()->sync($prerequisiteIds);

        return redirect()->route('pengelola.courses.index')
                        ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        // Optional: Add authorization check here if needed later (e.g., using Gates/Policies)
    
        // Get the name for the success message before deleting
        $courseName = $course->title;
    
        // Delete the course record from the database
        // Note: If you set up onDelete('cascade') correctly in your migrations
        // for related tables (like course_materials, enrollments, course_teacher),
        // those related records should be deleted automatically by the database.
        // However, physical files (like videos) in storage are NOT deleted automatically.
        $course->delete();
    
        // Redirect back to the course index page with a success message
        return redirect()->route('pengelola.courses.index')
                         ->with('success', "Course '{$courseName}' deleted successfully.");
    }

     /**
     * Show the form for assigning teachers to the specified course.
     *
     * @param Course $course The course to assign teachers to (route-model binding).
     * @return View
     */
    public function assignTeachersForm(Course $course): View
    {
        // Get all users who have the 'Teacher' role
        $allTeachers = User::role('Teacher')->orderBy('name')->get();

        // Get the IDs of the teachers currently assigned to this specific course
        // We use pluck() on the relationship to get only the IDs into an array
        $assignedTeacherIds = $course->teachers()->pluck('users.id')->toArray();

        // Return the view, passing the course, all teachers, and the assigned IDs
        // View file: resources/views/admin/courses/assign_teachers.blade.php (we create next)
        return view('pengelola.courses.assign_teachers', compact('course', 'allTeachers', 'assignedTeacherIds'));
    }

        /**
     * Update the teachers assigned to the specified course in storage.
     *
     * @param Request $request The incoming request containing teacher IDs.
     * @param Course $course The course to update assignments for.
     * @return RedirectResponse
     */
    public function syncTeachers(Request $request, Course $course): RedirectResponse
    {
        // Validate the incoming request
        // We expect 'teacher_ids' to be an array (it might be missing if no boxes are checked)
        // Each item in the array must be an integer and exist in the 'users' table.
        $validated = $request->validate([
            'teacher_ids' => 'nullable|array', // Allow empty submission to remove all teachers
            'teacher_ids.*' => 'required|integer|exists:users,id', // Ensure each ID is a valid user ID
        ]);

        // Use null coalescing operator to default to an empty array if 'teacher_ids' is not sent
        $teacherIds = $validated['teacher_ids'] ?? [];

        // Sync the relationship
        // The sync() method handles attaching new teachers and detaching removed ones automatically.
        $course->teachers()->sync($teacherIds);

        // Redirect back to where it makes sense (e.g., course list or course edit)
        return redirect()->route('pengelola.courses.index') // Or maybe admin.courses.edit?
                         ->with('success', 'Teacher assignments updated successfully for ' . $course->title);
    }
}
