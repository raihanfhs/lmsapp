<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): View
    {
        // We can pass data here later if needed
        return view('pengelola.dashboard'); // View file to be created
    }

    public function teacherEngagement()
    {
        // 1. Fetch all users who are teachers.
        // 2. Eager load the relationships we need to count:
        //    - teachingCourses: to count assigned courses.
        //    - teachingCourses.materials: to count all materials within those courses.
        //    - gradesGiven: the relationship we just created to count graded students.
        $teachers = User::role('Teacher') // This is the correct Spatie syntax
        ->with('teachingCourses.materials', 'gradesGiven')
        ->get();

        // Map the data into a simpler format for the view
        $teacherStats = $teachers->map(function ($teacher) {
            return (object) [
                'name' => $teacher->name,
                'email' => $teacher->email,
                'courses_count' => $teacher->teachingCourses->count(),
                'materials_count' => $teacher->teachingCourses->sum(function ($course) {
                    return $course->materials->count();
                }),
                'graded_students_count' => $teacher->gradesGiven->count(),
            ];
        });

        // 3. Return the view with the prepared stats
        return view('pengelola.dashboard.teacher_engagement', [
            'teacherStats' => $teacherStats
        ]);
    }

}