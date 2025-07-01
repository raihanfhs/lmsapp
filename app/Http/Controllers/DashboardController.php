<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Check user role and redirect
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        } elseif ($user->hasRole('pengelola')) {
            return redirect()->route('pengelola.dashboard');
        } elseif ($user->hasRole('chief')) { // <--- THIS IS CRITICAL
            // Add a dd() here to confirm this block is hit if you are a Chief
            // dd('User has chief role, redirecting to chief dashboard');
            return redirect()->route('chief.dashboard');
        }

        // Fallback for users with no specific dashboard role or generic users
        return view('dashboard');
    }
}