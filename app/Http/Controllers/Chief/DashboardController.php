<?php

namespace App\Http\Controllers\Chief;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course; // Make sure this is imported
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel; // <--- ADD THIS
use App\Exports\UsersExport;        // <--- ADD THIS
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DashboardReportExport; 

class DashboardController extends Controller
{
    public function index(): View
    {
        // ... (usersByRole data as before) ...
        $rolesWithUsers = Role::withCount('users')->get();
        $usersByRole = $rolesWithUsers->map(function ($role) {
            return (object) [
                'role' => $role->name,
                'count' => $role->users_count,
            ];
        });
        if ($usersByRole->isEmpty()) {
            $usersByRole = collect([
                (object)['role' => 'Teacher', 'count' => 5],
                (object)['role' => 'Student', 'count' => 30],
                (object)['role' => 'Admin', 'count' => 1],
                (object)['role' => 'Pengelola', 'count' => 2],
                (object)['role' => 'Chief', 'count' => 1],
            ]);
        }

        // 2. Data for User Verification Status (Pie Chart)
        $verifiedUsersCount = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsersCount = User::whereNull('email_verified_at')->count();
        $userVerificationStatus = [
            'labels' => ['Verified', 'Unverified'],
            'data' => [$verifiedUsersCount, $unverifiedUsersCount],
            'backgroundColor' => ['#4CAF50', '#FFC107'], // Green for Verified, Amber for Unverified
            'borderColor' => ['#4CAF50', '#FFC107'],
        ];

        // 3. Data for New User Registrations Over Time (Line Chart)
        $newUsersByMonth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $userRegistrationTrends = [
            'labels' => $newUsersByMonth->pluck('month')->toArray(),
            'data' => $newUsersByMonth->pluck('count')->toArray(),
            'label' => 'New User Registrations',
            'borderColor' => '#007bff', // Blue
            'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
        ];

        // 4. Data for Courses by Status (New Bar Chart)
        $coursesByStatus = Course::select('status', DB::raw('count(*) as count'))
                                 ->groupBy('status')
                                 ->get();

        $courseStatusData = [
            'labels' => $coursesByStatus->pluck('status')->map(function($status) {
                return ucfirst($status);
            })->toArray(),
            'data' => $coursesByStatus->pluck('count')->toArray(),
            'backgroundColor' => [
                'rgba(255, 99, 132, 0.7)', // Draft (Red)
                'rgba(54, 162, 235, 0.7)', // Published (Blue)
                'rgba(255, 206, 86, 0.7)'  // Archived (Yellow)
            ],
            'borderColor' => [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ]
        ];

        $data = $this->getDashboardChartData();

        return view('chief.dashboard', $data, compact(
            'usersByRole',
            'userVerificationStatus',
            'userRegistrationTrends',
            'courseStatusData'
        ));

    }

        /**
     * Export a comprehensive dashboard report to Excel with multiple sheets.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportDashboardExcel()
    {
        // Get all chart data
        $data = $this->getDashboardChartData();
        return Excel::download(new DashboardReportExport($data), 'executive_dashboard_report.xlsx');
    }

    /**
     * Export a comprehensive dashboard report to PDF.
     * @return \Illuminate\Http\Response
     */
    public function exportDashboardPdf()
    {
        // Get all chart data
        $data = $this->getDashboardChartData();
        $pdf = Pdf::loadView('reports.executive_dashboard_pdf', $data); // Use a new PDF view
        return $pdf->download('executive_dashboard_report.pdf');
    }

    /**
     * Private method to fetch all data required for dashboard charts and reports.
     * @return array
     */
    private function getDashboardChartData(): array
    {
        // 1. Data for Users by Role
        $rolesWithUsers = Role::withCount('users')->get();
        $usersByRole = $rolesWithUsers->map(function ($role) {
            return (object) [
                'role' => $role->name,
                'count' => $role->users_count,
            ];
        });
        if ($usersByRole->isEmpty()) {
            $usersByRole = collect([
                (object)['role' => 'Teacher', 'count' => 5],
                (object)['role' => 'Student', 'count' => 30],
                (object)['role' => 'Admin', 'count' => 1],
                (object)['role' => 'Pengelola', 'count' => 2],
                (object)['role' => 'Chief', 'count' => 1],
            ]);
        }

        // 2. Data for User Verification Status (Pie Chart)
        $verifiedUsersCount = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsersCount = User::whereNull('email_verified_at')->count();
        $userVerificationStatus = [
            'labels' => ['Verified', 'Unverified'],
            'data' => [$verifiedUsersCount, $unverifiedUsersCount],
            'backgroundColor' => ['#4CAF50', '#FFC107'],
            'borderColor' => ['#4CAF50', '#FFC107'],
        ];

        // 3. Data for New User Registrations Over Time (Line Chart)
        $newUsersByMonth = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $userRegistrationTrends = [
            'labels' => $newUsersByMonth->pluck('month')->toArray(),
            'data' => $newUsersByMonth->pluck('count')->toArray(),
            'label' => 'New User Registrations',
            'borderColor' => '#007bff',
            'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
        ];

        // 4. Data for Courses by Status (Bar Chart)
        $coursesByStatus = Course::select('status', DB::raw('count(*) as count'))
                                 ->groupBy('status')
                                 ->get();

        $courseStatusData = [
            'labels' => $coursesByStatus->pluck('status')->map(function($status) {
                return ucfirst($status);
            })->toArray(),
            'data' => $coursesByStatus->pluck('count')->toArray(),
            'backgroundColor' => [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)'
            ],
            'borderColor' => [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)'
            ]
        ];
        // Return all collected data as an associative array
        return [
            'usersByRole' => $usersByRole,
            'userVerificationStatus' => $userVerificationStatus,
            'userRegistrationTrends' => $userRegistrationTrends,
            'courseStatusData' => $courseStatusData,
        ];
    }

}