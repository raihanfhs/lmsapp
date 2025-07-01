<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse; // Import RedirectResponse
use Illuminate\View\View; // Import View

class DashboardController extends Controller
{
    public function index() // Removed : View type hint for now, in case of unexpected non-View return
    {
        // dd('DashboardController reached!'); // THIS ONE SHOULD HAVE APPEARED FIRST
        // If this message appeared, the controller is definitely being hit.

        $user = Auth::user();

        // dd('User fetched: ' . $user->email . ' Roles: ' . $user->getRoleNames()->implode(', ')); // THIS ONE CONFIRMS USER AND ROLES

        if ($user->hasRole('admin')) {
            // dd('Redirecting to Admin Dashboard'); // If you were admin, this would hit
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('teacher')) {
            // dd('Redirecting to Teacher Dashboard'); // If you were teacher, this would hit
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole('student')) {
            // dd('Redirecting to Student Dashboard'); // If you were student, this would hit
            return redirect()->route('student.dashboard');
        } elseif ($user->hasRole('pengelola')) {
            // dd('Redirecting to Pengelola Dashboard'); // If you were pengelola, this would hit
            return redirect()->route('pengelola.dashboard');
        } elseif ($user->hasRole('Chief')) { // CHANGE 'chief' to 'Chief'
            return redirect()->route('chief.dashboard');
        }

        // If none of the role checks above pass, it falls back to the generic dashboard
        dd('No specific role dashboard, showing generic dashboard.'); // This would hit if hasRole('chief') returned false
        return view('dashboard');
    }
}