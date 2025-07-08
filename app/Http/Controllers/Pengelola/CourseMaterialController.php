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
        // Otorisasi sudah ditangani oleh middleware pada route, jadi pengecekan manual tidak diperlukan.

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'integer',
                // Pastikan parent_id (jika ada) adalah materi yang valid di dalam course yang sama.
                \Illuminate\Validation\Rule::exists('course_materials', 'id')->where('course_id', $course->id)
            ],
            // Menentukan validasi file secara eksplisit dan jelas.
            // max:102400 artinya maksimal 100MB (100 * 1024 KB). Sesuaikan jika perlu.
            'video_file' => 'required|file|mimes:mp4,mov,avi,webm|max:102400',
        ]);

        // Proses penyimpanan file yang lebih rapi.
        // File akan disimpan di storage/app/public/course_materials/{course_id}/...
        $path = "course_materials/{$course->id}";
        $filePath = $request->file('video_file')->store($path, 'public');

        // Menyiapkan data untuk dimasukkan ke database.
        $data = [
            'course_id'   => $course->id,
            'parent_id'   => $validated['parent_id'] ?? null,
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path'   => $filePath,
            'file_type'   => 'video', // Tipe file bisa dibuat dinamis jika perlu.
        ];

        CourseMaterial::create($data);

        // Redirect kembali ke halaman detail kursus dengan pesan sukses.
        // Sebaiknya redirect ke route milik 'pengelola' bukan 'teacher'.
        return redirect()->route('pengelola.courses.show', $course->id)
                            ->with('success', 'Materi "' . $validated['title'] . '" berhasil diunggah!');
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