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
        // 1. Ambil data mentah
        $rawData = $this->getDashboardChartData();

        // 2. Siapkan array baru untuk data yang sudah diformat
        $dataForExcel = [];

        // 3. Loop dan ubah setiap set data ke format ['labels', 'data']
        foreach ($rawData as $key => $collection) {
            // Ambil nama kolom pertama (e.g., 'role', 'status', 'date') sebagai label
            $labelKey = !empty($collection->first()) ? array_keys((array) $collection->first())[0] : 'label';

            $dataForExcel[$key] = [
                'labels' => $collection->pluck($labelKey),
                'data'   => $collection->pluck('count'),
            ];
        }

        // 4. Kirim data yang sudah siap ke kelas Export
        return Excel::download(new DashboardReportExport($dataForExcel), 'executive_dashboard_report.xlsx');
    }

    /**
     * Export a comprehensive dashboard report to PDF.
     * @return \Illuminate\Http\Response
     */
    public function exportDashboardPdf()
    {
        // 1. Ambil data mentah
        $rawData = $this->getDashboardChartData();

        // 2. Siapkan data khusus untuk PDF
        $dataForPdf = [
            // Untuk tabel ini, view butuh data mentah. Ini akan memperbaiki error di PDF.
            'usersByRole' => $rawData['usersByRole'],

            // Untuk tabel-tabel lain, view butuh format ['labels', 'data'].
            'userVerificationStatus' => [
                'labels' => $rawData['userVerificationStatus']->pluck('status'),
                'data'   => $rawData['userVerificationStatus']->pluck('count'),
            ],
            'userRegistrationTrends' => [
                'labels' => $rawData['userRegistrationTrends']->pluck('date'),
                'data'   => $rawData['userRegistrationTrends']->pluck('count'),
            ],
            'courseStatusData' => [
                'labels' => $rawData['courseStatusData']->pluck('status'),
                'data'   => $rawData['courseStatusData']->pluck('count'),
            ],
        ];

        // 3. Kirim data yang sudah diolah ke view PDF
        $pdf = Pdf::loadView('reports.executive_dashboard_pdf', $dataForPdf)
                ->setPaper('a4', 'landscape');

        return $pdf->download('dashboard-report-' . now()->format('Y-m-d') . '.pdf');
    }
    /**
     * Private method to fetch all data required for dashboard charts and reports.
     * @return array
     */
    private function getDashboardChartData(): array
    {
        // Data 1: Users by Role
        $usersByRole = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->select('roles.name as role', DB::raw('count(users.id) as count'))
            ->groupBy('roles.name')
            ->get();

        // Data 2: User Verification Status
        $userVerificationStatus = User::select(
                DB::raw("CASE WHEN email_verified_at IS NOT NULL THEN 'Verified' ELSE 'Not Verified' END as status"),
                DB::raw('count(*) as count')
            )
            ->groupBy('status')
            ->get();

        // Data 3: New User Registrations Over Time
        $userRegistrationTrends = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Data 4: Courses by Status
        $courseStatusData = Course::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // KEMBALIKAN SEMUA DALAM BENTUK KOLEKSI MENTAH
        return [
            'usersByRole' => $usersByRole,
            'userVerificationStatus' => $userVerificationStatus,
            'userRegistrationTrends' => $userRegistrationTrends,
            'courseStatusData' => $courseStatusData,
        ];
    }

}