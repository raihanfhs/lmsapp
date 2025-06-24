<?php

namespace App\Http\Controllers\Teacher;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseMaterialController extends Controller
{
    /**
     * @param Course $course Automatically injected by route-model binding
     * @return View|RedirectResponse
     */
    public function create(Request $request, Course $course): View|RedirectResponse
    {
        // Authorization Check: Ensure the logged-in teacher is assigned to this course
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
                abort(403, 'You are not assigned to teach this course.');
        }
        $section_id = $request->query('section_id');
        return view('teacher.materials.create', compact('course', 'section_id'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        // Authorization Check
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
                abort(403, 'You are not assigned to teach this course.');
        }

        // --- CORRECTED VALIDATION ---
        $maxSize = (int)ini_get('upload_max_filesize') * 1024; // Get max size in Kilobytes

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
                // CRITICAL: This is the corrected 'max' rule.
                'max:' . $maxSize,
            ],
        ]);

        // --- SIMPLIFIED FILE HANDLING ---
        $path = "course_videos/{$course->id}";
        $filePath = $request->file('video_file')->store($path, 'public');

        // Create the CourseMaterial record in the database
        $course->materials()->create([
            'parent_id'   => $validated['parent_id'] ?? null,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path'   => $filePath,
            'file_type'   => 'video', // This will now save thanks to the fix in Step 1
        ]);

        // Redirect back to the course detail page with a success message
        return redirect()->route('teacher.courses.show', $course->id)
                            ->with('success', 'Material "' . $validated['title'] . '" uploaded successfully!');
    }

    public function edit(CourseMaterial $material): View
    {
        // Optional: Add authorization here if needed
        // Gate::authorize('update', $material);

        return view('teacher.materials.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseMaterial $material): RedirectResponse
    {
        // Optional: Add authorization here if needed
        // Gate::authorize('update', $material);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,jpg,png,zip|max:10240', // 10MB Max
        ]);

        // Handle file update
        if ($request->hasFile('file_path')) {
            // Delete the old file if it exists
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            // Store the new file
            $validated['file_path'] = $request->file('file_path')->store('course_materials', 'public');
        }

        $material->update($validated);

        return redirect()->route('teacher.courses.show', $material->course_id)->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseMaterial $material): RedirectResponse
    {
        // Optional: Add authorization here if needed
        // Gate::authorize('delete', $material);

        // Get the course ID before deleting the material to redirect back to the correct page
        $courseId = $material->course_id;
        
        // Delete the associated file from storage if it exists
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();

        return redirect()->route('teacher.courses.show', $courseId)->with('success', 'Material deleted successfully.');
    }    
}
