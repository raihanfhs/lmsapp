<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
// Import RedirectResponse for type hinting (optional but good practice)
use Illuminate\Http\RedirectResponse;


class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     * Redirects the user to the appropriate dashboard based on their role.
     *
     * @param  \Illuminate\Http\Request  $request // Parameter can be removed if not used
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->email === 'chief@lms.test') {
        return redirect()->route('chief.dashboard');
        }
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        } elseif ($user->hasRole('pengelola')) {
            return redirect()->route('pengelola.dashboard');
        } elseif ($user->hasRole('Chief')) { 
            return redirect()->route('chief.dashboard');
        }
        return view('dashboard');
    }
}