<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LearningPath; // Impor model
use Illuminate\Http\Request;
use Illuminate\View\View; // Impor View
use Illuminate\Http\RedirectResponse;


class LearningPathController extends Controller
{
    public function index(): View
    {
        // Ambil hanya learning path yang aktif
        // withCount('courses') untuk efisiensi, agar kita bisa tahu jumlah kursus tanpa query tambahan
        $learningPaths = LearningPath::where('is_active', true)
                                     ->withCount('courses')
                                     ->latest()
                                     ->paginate(9);

        return view('student.learning_paths.index', compact('learningPaths'));
    }

    public function show(LearningPath $learningPath): View
    {
        // Tolak akses jika path tidak aktif
        if (!$learningPath->is_active) {
            abort(404);
        }

        // Eager load relasi 'courses' untuk menghindari N+1 query problem di view
        $learningPath->load('courses');

        return view('student.learning_paths.show', compact('learningPath'));
    }

    public function enroll(Request $request, LearningPath $learningPath): RedirectResponse
    {
        $student = auth()->user();

        // Cek apakah student sudah terdaftar di path ini
        if ($student->enrolledLearningPaths()->where('learning_path_id', $learningPath->id)->exists()) {
            return redirect()->route('student.learning-paths.show', $learningPath)
                            ->with('info', 'You are already enrolled in this learning path.');
        }

        // 1. Daftarkan student ke Learning Path
        $student->enrolledLearningPaths()->attach($learningPath->id);

        // 2. Daftarkan student ke semua course di dalam path tersebut
        $courseIds = $learningPath->courses->pluck('id');
        $student->enrolledCourses()->syncWithoutDetaching($courseIds);

        return redirect()->route('student.dashboard') // Arahkan ke dashboard setelah berhasil
                        ->with('success', 'Successfully enrolled in ' . $learningPath->title);
    }

}