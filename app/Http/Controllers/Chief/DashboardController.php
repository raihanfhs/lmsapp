<?php

namespace App\Http\Controllers\Chief;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(): View
    {
        dd('Chief Dashboard Controller hit!'); // <--- ADD THIS LINE HERE

        // ... rest of your code ...
        // (You can keep the corrected query logic from before)

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

        return view('chief.dashboard', compact('usersByRole'));
    }
}