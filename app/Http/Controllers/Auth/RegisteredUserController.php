<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile; // <-- Import the Profile model
use App\Models\StudentDetail; // <-- Import the StudentDetail model
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
// Import Role if you need it directly, but assignRole() works without it here
// use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // This method likely already exists from Breeze
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // --- Step 1: Validate Input ---
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // --- Step 2: Create User ---
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Breeze might add email_verified_at = now(); here if email verification IS NOT required by default
            // If MustVerifyEmail is used, this should be null initially.
        ]);

        // --- Step 3: Assign Default Role (Student) --- ADD THIS
        $user->assignRole('Student');

        // --- Step 4: Create Initial Related Records --- ADD THESE
        // Create an empty profile record linked to the user
        Profile::create(['user_id' => $user->id]);
        // Create an empty student detail record linked to the user
        StudentDetail::create(['user_id' => $user->id]);

        // --- Step 5: Fire Registered Event --- (Existing)
        event(new Registered($user));

        // --- Step 6: Log the User In --- (Existing)
        Auth::login($user);

        // --- Step 7: Redirect --- (Existing)
        // Redirects to the '/dashboard' route, which our DashboardController handles
        return redirect(route('dashboard', absolute: false));
    }
}