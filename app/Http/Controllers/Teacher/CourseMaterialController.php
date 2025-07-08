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
use App\Notifications\NewCourseMaterial;
use Illuminate\Support\Facades\Notification;

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
        // Otorisasi Anda sudah benar
        if (!Auth::user()->teachingCourses()->where('course_id', $course->id)->exists()) {
            abort(403, 'You are not assigned to this course.');
        }

        // --- Validasi yang sedikit disederhanakan ---
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video_url,document_file,image_file',
            'content_url' => 'required_if:type,video_url|nullable|url',
            'content_file' => [
                'required_if:type,document_file,image_file',
                'nullable',
                'file',
                // Aturan mime bisa disesuaikan lagi jika perlu
                'mimes:pdf,doc,docx,ppt,pptx,jpeg,png,jpg,gif,svg',
                'max:10240' // Max 10MB, sesuaikan jika perlu
            ],
        ]);

        $type = $validated['type'];
        $content = '';

        if ($type === 'video_url') {
            $content = $validated['content_url'];
        } elseif (in_array($type, ['document_file', 'image_file'])) {
            $path = "course_materials/{$course->id}";
            $content = $request->file('content_file')->store($path, 'public');
        }

        $lastOrder = $course->materials()->max('order');

        // --- MODIFIKASI INTI DI SINI ---
        // Simpan material yang baru dibuat ke dalam variabel $material
        $material = $course->materials()->create([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'type'        => $type,
            'content'     => $content,
            'order'       => $lastOrder + 1,
        ]);

        // --- LOGIKA NOTIFIKASI DIMULAI ---
        $students = $course->students;
        if ($students->isNotEmpty()) {
            // Kirim notifikasi menggunakan kelas yang sudah kita buat
            Notification::send($students, new NewCourseMaterial($course, $material));
        }
        // --- LOGIKA NOTIFIKASI SELESAI ---

        return redirect()->route('teacher.courses.show', $course->id)
                        ->with('success', 'Material added successfully!');
    }
    public function edit(Course $course, CourseMaterial $material): View 
    {

        
        return view('teacher.materials.edit', compact('course', 'material')); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course, CourseMaterial $material): RedirectResponse
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
    public function destroy(Course $course, CourseMaterial $material): RedirectResponse
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