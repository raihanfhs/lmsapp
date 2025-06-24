<?php

// Declare the namespace correctly within the Teacher folder
namespace App\Http\Controllers\Teacher;

// Import the base Controller
use App\Http\Controllers\Controller;
// Import Request if needed later
use Illuminate\Http\Request;
// Import View response type
use Illuminate\View\View;
// Import Auth facade to get the logged-in user
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the teacher dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Get the authenticated user (the teacher)
        $user = Auth::user();

        // Get count of courses created by this teacher using the relationship defined in User model
        $courseCount = $user->teachingCourses()->count();

        // Return the teacher dashboard view, passing the data
        // View file: resources/views/teacher/dashboard.blade.php (created in Phase 7)
        return view('teacher.dashboard', [
            'courseCount' => $courseCount,
        ]);
    }
}