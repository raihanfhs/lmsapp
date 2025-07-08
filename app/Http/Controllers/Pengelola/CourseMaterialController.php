<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseMaterialController extends Controller
{
    public function create(Course $course): View
    {
        $parentMaterials = CourseMaterial::where('course_id', $course->id)
                                        ->whereNull('parent_id')
                                        ->orderBy('order')
                                        ->get();

        return view('pengelola.materials.create', compact('course', 'parentMaterials'));
    }

   public function store(Request $request, Course $course): RedirectResponse
    {
        // Authorization Check: Ensure the logged-in teacher is assigned to this course
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
                abort(403, 'You are not assigned to teach this course.');
        }

        // --- CORRECTED VALIDATION ---
        // Get max file size from your server's PHP configuration (in kilobytes)
        $maxSize = (int)ini_get('upload_max_filesize') * 1024;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'integer',
                \Illuminate\Validation\Rule::exists('course_materials', 'id')->where('course_id', $course->id)
            ],
            'video_file' => [
                'required',
                'file',
                'mimes:mp4,mov,avi,wmv,mpeg,qt,webm',
                // CRITICAL FIX: This is the corrected 'max' rule.
                'max:' . $maxSize,
            ],
        ]);

        // --- FILE HANDLING ---
        $path = "course_videos/{$course->id}";
        $filePath = $request->file('video_file')->store($path, 'public');

        // Prepare data for database record
        $data = [
            'course_id'   => $course->id,
            'parent_id'   => $validated['parent_id'] ?? null,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path'   => $filePath,
            'file_type'   => 'video',
        ];

        // Create the CourseMaterial record
        CourseMaterial::create($data);

        // Redirect to the course detail page so the teacher can see the new material
        return redirect()->route('teacher.courses.show', $course->id)
                            ->with('success', 'Material "' . $validated['title'] . '" uploaded successfully!');
    }

    public function edit(Course $course, CourseMaterial $material): View
    {
        if ($material->course_id !== $course->id) {
             abort(404);
        }

        $parentMaterials = CourseMaterial::where('course_id', $course->id)
                                        ->whereNull('parent_id')
                                        ->where('id', '!=', $material->id)
                                        ->orderBy('order')
                                        ->get();

        return view('pengelola.materials.edit', compact('course', 'material', 'parentMaterials'));
    }

    public function update(Request $request, Course $course, CourseMaterial $material): RedirectResponse
    {
        if ($material->course_id !== $course->id) {
             abort(404);
        }

        $validated = $request->validate([
             'title' => 'required|string|max:255',
             'description' => 'nullable|string',
             'parent_id' => ['nullable', 'integer', Rule::exists('course_materials', 'id')->where('course_id', $course->id), Rule::notIn([$material->id])],
         ]);

        $material->update($validated);

        return redirect()->route('pengelola.courses.show', $course->id)
                         ->with('success', 'Material updated successfully.');
    }

    public function destroy(Course $course, CourseMaterial $material): RedirectResponse
    {
        if ($material->course_id !== $course->id) {
            abort(404);
        }

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()->route('pengelola.courses.show', $course->id)
                        ->with('success', 'Material successfully deleted!');
    }
}