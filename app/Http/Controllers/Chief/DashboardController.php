<?php

namespace App\Http\Controllers\Chief;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Eager load the 'courses' relationship and count them for each division
        $divisions = Division::withCount('courses')->get();

        // Prepare the data specifically for the chart
        $chartData = [
            'labels' => $divisions->pluck('name'), // e.g., ['Data Science', 'Business', 'Design']
            'data' => $divisions->pluck('courses_count'), // e.g., [5, 8, 3]
        ];

        // Pass the prepared chart data to the view
        return view('chief.dashboard', compact('chartData'));
    }

}