<?php

namespace App\Http\Controllers\Chief;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Sama seperti di artikel, kita siapkan query untuk mengambil data.
        // Kita mengambil jumlah pengguna, dikelompokkan berdasarkan role.
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
                            ->whereNotNull('role')
                            ->groupBy('role')
                            ->get();

        // Jika data dari database kosong, kita buat data dummy agar chart tetap muncul.
        if ($usersByRole->isEmpty()) {
            $usersByRole = collect([
                ['role' => 'teacher', 'count' => 5],
                ['role' => 'student', 'count' => 30],
                ['role' => 'admin', 'count' => 1],
            ]);
        }

        // Langsung kirim collection-nya ke view, sama seperti di artikel.
        return view('chief.dashboard', compact('usersByRole'));
    }
}
