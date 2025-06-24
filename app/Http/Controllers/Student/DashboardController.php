<?php

// Declare the namespace correctly within the Student folder
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Impor Auth
use Illuminate\View\View;
class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function __invoke(): View
    {
        $student = Auth::user();

        // Ambil data learning path yang diikuti
        $enrolledLearningPaths = $student->enrolledLearningPaths()
                                      ->withCount('courses')
                                      ->get();

        // Ambil juga data kursus yang diikuti jika Anda masih ingin menampilkannya
        $enrolledCoursesCount = $student->enrolledCourses()->count();

        // Tentukan view yang akan ditampilkan
        $viewName = 'student.dashboard'; // atau 'dashboard' tergantung nama file Anda

        // Kirim SEMUA data yang diperlukan ke view
        return view($viewName, compact('enrolledLearningPaths', 'enrolledCoursesCount'));
    }
}