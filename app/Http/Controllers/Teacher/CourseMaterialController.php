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

class CourseMaterialController extends Controller
{
    public function create(Course $course): View
    {
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
             abort(403, 'You are not assigned to this course.');
        }
        return view('teacher.materials.create', compact('course'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to this course.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video_url,document_file,image_file',
        ]);

        $content = '';
        $type = $request->input('type');

        if ($type === 'video_url') {
            $validatedUrl = $request->validate(['content_url' => 'required|url']);
            $content = $validatedUrl['content_url'];
        }

        if (in_array($type, ['document_file', 'image_file'])) {
            $rules = ['content_file' => 'required|file'];
            if ($type === 'document_file') {
                $rules['content_file'] .= '|mimes:pdf,doc,docx,ppt,pptx|max:10240';
            }
            if ($type === 'image_file') {
                $rules['content_file'] .= '|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            }
            $request->validate($rules);
            $path = "course_materials/{$course->id}";
            $content = $request->file('content_file')->store($path, 'public');
        }

        $lastOrder = $course->materials()->max('order');

        $course->materials()->create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'type'        => $type,
            'content'     => $content,
            'order'       => $lastOrder + 1,
        ]);

        return redirect()->route('teacher.courses.show', $course->id)
                         ->with('success', 'Material added successfully!');
    }
    public function edit(CourseMaterial $material): View
    {
        // This is a placeholder. We will build this out later.
        return view('teacher.materials.edit', compact('material'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseMaterial $material): RedirectResponse
    {
        // This method will need to be updated later to support the new multi-type system.
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $material->update($validated);

        return redirect()->route('teacher.courses.show', $material->course_id)->with('success', 'Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourseMaterial $material): RedirectResponse
    {
        $courseId = $material->course_id;
        
        // If the material is a file, delete it from storage
        if (in_array($material->type, ['document_file', 'image_file'])) {
            Storage::disk('public')->delete($material->content);
        }
        
        $material->delete();

        return redirect()->route('teacher.courses.show', $courseId)->with('success', 'Material deleted successfully.');
    }
}