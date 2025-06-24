<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\LearningPath; // <--- Impor model LearningPath
use Illuminate\Http\Request;
use Illuminate\View\View; // <--- Impor class View
use App\Http\Requests\StoreLearningPathRequest; // <--- TAMBAHKAN INI
use Illuminate\Http\RedirectResponse; // <--- TAMBAHKAN INI
use Illuminate\Support\Str;
use App\Http\Requests\UpdateLearningPathRequest;
use App\Models\Course;

class LearningPathController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View // <--- Tambahkan method ini
    {
        $learningPaths = LearningPath::latest()->paginate(10); // Ambil data, urutkan dari yang terbaru, 10 per halaman

        return view('pengelola.learning_paths.index', compact('learningPaths'));
    }

    public function create(): View  // <--- TAMBAHKAN METHOD INI
    {
        return view('pengelola.learning_paths.create');
    }

    public function store(StoreLearningPathRequest $request): RedirectResponse // <--- TAMBAHKAN METHOD INI
    {
        // Ambil data yang sudah divalidasi
        $validated = $request->validated();

        // Tambahkan slug ke data yang divalidasi
        $validated['slug'] = Str::slug($validated['title']);

        // Buat record baru di database
        LearningPath::create($validated);

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('pengelola.learning-paths.index')
                         ->with('success', 'Learning path created successfully.');
    }
    public function edit(LearningPath $learningPath): View // <--- TAMBAHKAN METHOD INI
    {
        $courses = Course::all();
        $assignedCourseIds = $learningPath->courses->pluck('id')->toArray();
        return view('pengelola.learning_paths.edit', compact('learningPath', 'courses', 'assignedCourseIds'));
    }
    public function update(UpdateLearningPathRequest $request, LearningPath $learningPath): RedirectResponse
    {
        // Ambil data yang sudah divalidasi
        $validated = $request->validated();

        // Jika title berubah, buat ulang slug-nya
        $validated['slug'] = Str::slug($validated['title']);

        // Update record di database (ini tetap sama)
        $learningPath->update($validated);

        // ***** TAMBAHKAN BARIS INI UNTUK MENYIMPAN HUBUNGAN KURSUS *****
        // Sinkronkan (sync) kursus-kursus yang dipilih dari checkbox
        $learningPath->courses()->sync($request->input('courses', []));
        // ***** BATAS AKHIR BARIS BARU *****

        // Redirect kembali ke halaman index dengan pesan sukses (ini tetap sama)
        return redirect()->route('pengelola.learning-paths.index')
                        ->with('success', 'Learning path updated successfully.');
    }

    public function destroy(LearningPath $learningPath): RedirectResponse // <--- TAMBAHKAN METHOD INI
    {
        // Hapus data dari database
        $learningPath->delete();

        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('pengelola.learning-paths.index')
                        ->with('success', 'Learning path deleted successfully.');
    }
}