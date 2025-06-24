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
use App\Models\CourseSection;

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,jpg,png,zip|max:10240', // 10MB Max
            'course_section_id' => 'required|exists:course_sections,id',
        ]);

        // Handle the file upload
        $filePath = null;
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')->store('course_materials', 'public');
        }

        // Determine the order for the new material within the section
        $section = CourseSection::find($validated['course_section_id']);
        $lastOrder = $section->materials()->max('order');

        // Create the material
        $course->materials()->create([
            'course_section_id' => $validated['course_section_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'order' => $lastOrder + 1,
        ]);

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Material added successfully.');
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
