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
use App\Exports\CourseProgressExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Models\CertificateTemplate;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use Illuminate\Support\Arr; 


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
    public function store(StoreCourseRequest $request): RedirectResponse
    {
        // Validasi sudah terjadi secara otomatis!
        $validated = $request->validated();

        // Buat kursus
        $course = Course::create(\Illuminate\Support\Arr::except($validated, ['prerequisites']));

        // Attach prerequisites
        if (isset($validated['prerequisites'])) {
            $course->prerequisites()->sync($validated['prerequisites']);
        }

        return redirect()->route('pengelola.courses.index')
                        ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): View
    {
        // Eager load the materials and their children to prevent performance issues
        $course->load(['materials.children']);
        return view('pengelola.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course): View
    {
        // Fetch all courses EXCEPT the current one
        $allCourses = Course::where('id', '!=', $course->id)->orderBy('title')->get();

        $certificateTemplates = CertificateTemplate::all();

        $prerequisiteIds = $course->prerequisites()->pluck('courses.id')->toArray();

        return view('pengelola.courses.edit', compact('course', 'allCourses', 'prerequisiteIds', 'certificateTemplates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        // 1. Validasi sekarang terjadi otomatis saat memanggil method ini.
        //    Kita tidak perlu lagi blok $request->validate().

        // 2. Ambil semua data yang sudah tervalidasi.
        $validated = $request->validated();

        // 3. Update data kursus, kecuali 'prerequisites'.
        $course->update(Arr::except($validated, ['prerequisites']));

        // 4. Sinkronkan prerequisites secara terpisah.
        $course->prerequisites()->sync($validated['prerequisites'] ?? []);

        return redirect()->route('pengelola.courses.index')
                        ->with('success', 'Course updated successfully.');
    }


    public function destroy(Course $course): RedirectResponse
    {
        $courseName = $course->title;

        // Hapus thumbnail dari storage jika ada
        if ($course->thumbnail) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

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

    public function updateStatus(Request $request, Course $course): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::in([Course::STATUS_PUBLISHED, Course::STATUS_ARCHIVED, Course::STATUS_DRAFT])],
        ]);

        $course->update(['status' => $request->status]);

        return redirect()->route('pengelola.courses.index')->with('success', 'Course status has been updated successfully.');
    }

    /**
     * Display the progress of all students in a specific course.
     */
    public function progress(Course $course)
    {
        // Eager load relasi yang dibutuhkan secara efisien
        $enrollments = $course->enrollments()
                            ->with(['student', 'grade']) // Asumsi ada relasi 'grade' di model Enrollment
                            ->get();

        // Mapping sekarang tidak akan memicu query baru
        $studentsProgress = $enrollments->map(function ($enrollment) {
            return (object)[
                'name' => $enrollment->student->name,
                'email' => $enrollment->student->email,
                'enrolled_at' => $enrollment->created_at->format('d M Y'),
                'grade' => $enrollment->grade->grade ?? 'Not Graded',
                'status' => isset($enrollment->grade) ? ($enrollment->grade->is_passed ? 'Passed' : 'Failed') : 'In Progress',
            ];
        });

        return view('pengelola.courses.progress', compact('course', 'studentsProgress'));
    }

    public function exportProgress(Course $course)
    {
        // Generate a clean, URL-friendly version of the course title for the filename.
        $safeTitle = Str::slug($course->title, '-');

        // Define the filename for the downloaded Excel file.
        $fileName = 'progress-' . $safeTitle . '.xlsx';

        // Trigger the download.
        // This creates a new instance of our export class and passes the course to it.
        // The Excel package handles the rest.
        return Excel::download(new CourseProgressExport($course), $fileName);
    }

}
