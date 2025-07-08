<?php

namespace App\Http\Controllers\Chief;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course; 
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\UsersExport;        
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DashboardReportExport; 
use App\Models\Login;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Panggil method untuk mendapatkan semua data chart
        $chartData = $this->getDashboardChartData();

        // Ambil data untuk Siswa Aktif Harian (Daily Active Students)
        $thirtyDaysAgo = now()->subDays(30);
        $dailyActiveStudents = Login::where('created_at', '>=', $thirtyDaysAgo)
            ->whereHas('user.roles', function ($query) {
                $query->where('name', 'student');
            })
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(DISTINCT user_id) as student_count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
            
        $dasLabels = $dailyActiveStudents->pluck('date');
        $dasData = $dailyActiveStudents->pluck('student_count');

        // Gabungkan semua data dan kirim ke view
        return view('chief.dashboard', [
            'usersByRole' => $chartData['usersByRole'],
            'userVerificationStatus' => $chartData['userVerificationStatus'],
            'userRegistrationTrends' => $chartData['userRegistrationTrends'],
            'courseStatusData' => $chartData['courseStatusData'],
            'dasLabels' => $dasLabels,
            'dasData' => $dasData,
        ]);
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
    public function exportDashboardPdf(): Response
    {
        $chartData = $this->getDashboardChartData();
        $usersByRole = $chartData['userVerificationStatus']; // Ambil data

        // -- TAMBAHKAN BLOK KODE INI UNTUK MEMPERBAIKI PDF --
        $userVerificationStatusForPdf = [
            'labels' => array_column($chartData['userVerificationStatus'], 'status'),
            'data' => array_column($chartData['userVerificationStatus'], 'count')
        ];
        // -- AKHIR BLOK --

        $pdf = Pdf::loadView('reports.dashboard-pdf', [
            'usersByRole' => $chartData['usersByRole'],
            'userVerificationStatus' => $userVerificationStatusForPdf, // <-- GUNAKAN VARIABEL BARU
            'userRegistrationTrends' => $chartData['userRegistrationTrends'],
            'courseStatusData' => $chartData['courseStatusData'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('dashboard-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Private method to fetch all data required for dashboard charts and reports.
     * @return array
     */
    private function getDashboardChartData(): array
    {
        // Chart 1: Total Users by Role (No change needed here)
        $usersByRole = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name as role', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->get();

        // Chart 2: User Verification Status (CORRECTED LOGIC)
        $userVerificationStatus = User::select('email_verified_at', DB::raw('count(*) as count'))
            ->groupBy('email_verified_at')
            ->get()
            ->map(function ($item) {
                return [
                    // This creates the 'status' and 'count' keys JS expects
                    'status' => $item->email_verified_at ? 'Verified' : 'Not Verified',
                    'count' => $item->count,
                ];
            });
        

        // Chart 3: New User Registrations Over Time (No change needed here)
        $userRegistrationTrends = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Chart 4: Courses by Status (No change needed here)
        $courseStatusData = Course::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Return all chart data
        return [
            'usersByRole' => $usersByRole,
            'userVerificationStatus' => $userVerificationStatus,
            'userRegistrationTrends' => $userRegistrationTrends,
            'courseStatusData' => $courseStatusData,
        ];
    }

}