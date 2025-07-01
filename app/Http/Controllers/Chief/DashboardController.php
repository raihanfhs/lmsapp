<?php

namespace App\Http\Controllers\Chief;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;
use Illuminate\View\View;
use App\Models\Enrollment; 
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // --- Data for Pie Chart (Courses by Division) ---
        $divisions = Division::withCount('courses')->get();
        $coursesByDivisionData = [
            'labels' => $divisions->pluck('name'),
            'data' => $divisions->pluck('courses_count'),
        ];

        // --- Data for Line Chart (Enrollments over Time) ---
        $enrollmentCounts = Enrollment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        $enrollmentLabels = [];
        $enrollmentData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $enrollmentLabels[] = Carbon::parse($date)->format('M d'); // Format as "Jul 01"
            $enrollmentData[] = $enrollmentCounts[$date] ?? 0;
        }

        $enrollmentsOverTimeData = [
            'labels' => $enrollmentLabels,
            'data' => $enrollmentData,
        ];


        // Pass all chart data to the view
        return view('chief.dashboard', compact('coursesByDivisionData', 'enrollmentsOverTimeData'));
    }
}