<?php

namespace App\Http\Controllers\Chief;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role; // <--- ADD THIS LINE

class DashboardController extends Controller
{
    public function index(): View
    {
        // Fetch roles and eager load the count of users for each role
        // This is the correct way to get user counts per role with Spatie
        $rolesWithUsers = Role::withCount('users')->get(); //

        // Map the data to the format expected by your Chart.js script
        $usersByRole = $rolesWithUsers->map(function ($role) {
            return (object) [ // Cast to object if your Blade uses object property access
                'role' => $role->name,
                'count' => $role->users_count, // `users_count` is added by `withCount('users')`
            ];
        });

        // If there are no roles or no users assigned to roles, provide dummy data
        if ($usersByRole->isEmpty()) {
            $usersByRole = collect([
                (object)['role' => 'Teacher', 'count' => 5],
                (object)['role' => 'Student', 'count' => 30],
                (object)['role' => 'Admin', 'count' => 1],
                (object)['role' => 'Pengelola', 'count' => 2], //
                (object)['role' => 'Chief', 'count' => 1], //
            ]);
        }

        // dd($usersByRole); // Keep this dd() for YOUR testing, then remove it.

        return view('chief.dashboard', compact('usersByRole'));
    }
}