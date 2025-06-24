<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller; // Base Controller
use App\Models\Course;             // To receive the Course model
use App\Models\CourseMaterial;     // To fetch existing materials (for parent selection)
use Illuminate\Http\Request;       // Request object
use Illuminate\Support\Facades\Auth; // To check logged in user
use Illuminate\View\View;          // For returning views
use Illuminate\Http\RedirectResponse; // For redirects
use Illuminate\Support\Facades\Storage; 
use Illuminate\Validation\Rule;

class CourseMaterialController extends Controller
{
        /**
     * Show the form for creating a new course material for a specific course.
     *
     * @param Course $course Automatically injected by route-model binding
     * @return View|RedirectResponse
     */
    public function create(Course $course): View|RedirectResponse
    {
        // Authorization Check: Ensure the logged-in teacher is assigned to this course
        // We check if the course exists in the list of courses the user is teaching.
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
             abort(403, 'You are not assigned to teach this course.');
        }

        // Fetch existing top-level materials for this course to populate parent dropdown
        $parentMaterials = CourseMaterial::where('course_id', $course->id)
                                        ->whereNull('parent_id') // Only get top-level items
                                        ->orderBy('order')
                                        ->get();

        // Return the view for the create form, passing the course and potential parents
        // View file: resources/views/teacher/materials/create.blade.php
        return view('teacher.materials.create', compact('course', 'parentMaterials'));
    }

        /**
     * Store a newly created course material in storage.
     *
     * @param Request $request // Use a specific StoreMaterialRequest later
     * @param Course $course Automatically injected course from the route
     * @return RedirectResponse
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        // Authorization Check: Ensure the logged-in teacher is assigned to this course
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
             abort(403, 'You are not assigned to teach this course.');
        }

        // TODO: Move validation to a StoreMaterialRequest later
        // Determine max file size from php.ini (in kilobytes)
        $maxSize = (int)ini_get('upload_max_filesize') * 1024; // Convert M to KB

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Ensure parent_id exists in course_materials table AND belongs to the *same course*
            'parent_id' => ['nullable', 'integer', Rule::exists('course_materials', 'id')->where('course_id', $course->id)],
            'video_file' => [
                'required',
                'file',
                // Common video MIME types or specific extensions
                'mimes:mp4,mov,avi,wmv,mpeg,qt,webm',
                // Max file size validation based on php.ini (in KB)
                'max: 262144' . $maxSize
            ],
        ]);

        // Handle File Upload
        $filePath = null;
        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $path = "course_videos/{$course->id}"; // Store in folder like 'storage/app/public/course_videos/5/'
            // Store the file using store() which generates a unique name automatically
            $filePath = $request->file('video_file')->store($path, 'public');

            // Alternative: store with original name (less safe for conflicts)
            // $fileName = $request->file('video_file')->getClientOriginalName();
            // $filePath = $request->file('video_file')->storeAs($path, $fileName, 'public');
        } else {
             // Handle error - validation should prevent this, but good practice
             return back()->with('error', 'Video file upload failed.')->withInput();
        }


        // Prepare data for database record (excluding the file itself)
        $data = [
            'course_id' => $course->id,
            'parent_id' => $validated['parent_id'] ?? null, // Handle nullable parent_id
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null, // Handle nullable description
            'file_path' => $filePath,
            'file_type' => 'video', // Set file type
            // 'order' could be set here if needed, default is 0
        ];

        // Create the CourseMaterial record
        CourseMaterial::create($data);

        // Redirect after successful upload
        // TODO: Redirect to a course detail page or material list page later
        return redirect()->route('teacher.courses.index') // Redirecting to course list for now
                         ->with('success', 'Material "' . $validated['title'] . '" uploaded successfully!');
    }

        /**
     * Show the form for editing the specified course material.
     *
     * @param Course $course The course the material belongs to.
     * @param CourseMaterial $material The specific material to edit.
     * @return View|RedirectResponse
     */
    public function edit(Course $course, CourseMaterial $material): View|RedirectResponse
    {
        // Authorization Check 1: Ensure teacher is assigned to this course.
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
             abort(403, 'You are not assigned to teach this course.');
        }
        // Authorization Check 2: Ensure the material actually belongs to this course
        if ($material->course_id !== $course->id) {
             abort(403, 'Material does not belong to this course.');
        }

        // Fetch potential parent materials for the dropdown, excluding the current material itself
        $parentMaterials = CourseMaterial::where('course_id', $course->id)
                                        ->whereNull('parent_id')
                                        ->where('id', '!=', $material->id) // Cannot be its own parent
                                        ->orderBy('order')
                                        ->get();

        // Return the edit view
        // View file: resources/views/teacher/materials/edit.blade.php (we create next)
        return view('teacher.materials.edit', compact('course', 'material', 'parentMaterials'));
    }

    /**
     * Update the specified course material in storage.
     *
     * @param Request $request // Use UpdateMaterialRequest later
     * @param Course $course
     * @param CourseMaterial $material
     * @return RedirectResponse
     */
    public function update(Request $request, Course $course, CourseMaterial $material): RedirectResponse
    {
        // Authorization Check 1: Ensure teacher is assigned to this course.
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
             abort(403, 'You are not assigned to teach this course.');
        }
        // Authorization Check 2: Ensure the material belongs to this course
        if ($material->course_id !== $course->id) {
             abort(403, 'Material does not belong to this course.');
        }

        // TODO: Move validation to UpdateMaterialRequest later
        $validated = $request->validate([
             'title' => 'required|string|max:255',
             'description' => 'nullable|string',
             // Ensure parent_id exists in course_materials for this course, AND is not the material itself
             'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('course_materials', 'id')->where('course_id', $course->id),
                Rule::notIn([$material->id]) // Prevent setting itself as parent
             ],
         ]);

        // Update the material record with validated data (file is not changed here)
        $material->update($validated);

        // Redirect back to the course detail page
        return redirect()->route('teacher.courses.show', $course->id)
                         ->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified course material from storage and database.
     *
     * @param Course $course
     * @param CourseMaterial $material
     * @return RedirectResponse
     */
    public function destroy(Course $course, CourseMaterial $material): RedirectResponse
    {
         dd('Berhasil masuk ke method destroy');
        // Authorization Check 1: Pastikan guru ini memang mengajar course tersebut.
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to teach this course.');
        }

        // Authorization Check 2: Pastikan materi ini benar-benar milik course yang sedang diakses.
        if ($material->course_id !== $course->id) {
            // 404 (Not Found) lebih sesuai di sini karena materi tidak ditemukan dalam konteks course ini.
            abort(404);
        }

        // [PENTING] Hapus file fisik dari storage jika ada.
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        // Hapus record dari database
        $material->delete();

        // Redirect kembali ke halaman detail course dengan pesan sukses
        return redirect()->route('teacher.courses.show', $course->id)
                        ->with('success', 'Materi berhasil dihapus!');
    }
    
}
