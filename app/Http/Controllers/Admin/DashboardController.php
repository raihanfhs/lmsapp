<?php

// Declare the namespace correctly within the Admin folder
namespace App\Http\Controllers\Admin;

// Import the base Controller
use App\Http\Controllers\Controller;
// Import Request if needed later
use Illuminate\Http\Request;
// Import View response type
use Illuminate\View\View;
// Import any Models needed to fetch data for the dashboard
use App\Models\User;


class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Example: Get some data to pass to the view
        $userCount = User::count();
        $teacherCount = User::whereHas('roles', fn($query) => $query->where('name', 'Teacher'))->count();
        $studentCount = User::whereHas('roles', fn($query) => $query->where('name', 'Student'))->count();

        // Return the admin dashboard view, passing the data
        // The view file will be resources/views/admin/dashboard.blade.php (we create this in Phase 7)
        return view('admin.dashboard', [
            'userCount' => $userCount,
            'teacherCount' => $teacherCount,
            'studentCount' => $studentCount,
        ]);
    }
}