<?php

namespace App\Http\Controllers;

// Core Laravel imports for requests, responses, auth, redirects, views
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Skill;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile; 
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     * Loads the user with their profile and role-specific details.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        // Eager load common relations
        $user->load(['profile', 'studentDetail', 'teacherDetail']);
    
        $allSkills = []; // Initialize as empty array
        $userSkillIds = []; // Initialize as empty array
    
        // If the user is a Teacher, fetch all available skills and their current skills
        if ($user->hasRole('Teacher')) {
            $allSkills = Skill::orderBy('name')->get();
            // Eager load skills relationship for the user if it's not already loaded often
            // or if you prefer to do it explicitly here:
            $user->load('skills');
            $userSkillIds = $user->skills->pluck('id')->toArray();
        }
    
        return view('profile.edit', [
            'user' => $user,
            'allSkills' => $allSkills, // Pass all skills to the view
            'userSkillIds' => $userSkillIds, // Pass the IDs of skills the user already has
            // 'status' => session('status'), // Breeze's default profile.edit also expects this for the session message
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Get the currently authenticated user
        $user = $request->user();

        // --- 1. Update User's Name and Email ---
        $user->fill($request->safe()->only(['name', 'email']));

        // If email was changed, reset email verification status
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save(); // Save changes to the user model


        // --- 2. Update/Create Common Profile Data (Bio, Phone, Address) ---
        $profileData = $request->safe()->only([
            'bio', 'phone_number', 'address_line_1', 'address_line_2',
            'city', 'state', 'postal_code', 'country'
        ]);
        // Use updateOrCreate: finds profile by user_id or creates a new one if not found
        $profile = $user->profile()->updateOrCreate(
            ['user_id' => $user->id], // Attributes to find the record by
            $profileData              // Attributes to update or create with
        );


        // --- 3. Handle Avatar Upload ---
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Define storage path within storage/app/public/
            $path = 'avatars';
            // Generate a unique file name (e.g., user_id_timestamp.ext)
            $fileName = $user->id . '_' . time() . '.' . $request->file('avatar')->extension();

            // Store the new file in storage/app/public/avatars
            $newPath = $request->file('avatar')->storeAs($path, $fileName, 'public');

            // Delete the old avatar if it exists and is different from the new one
            if ($profile->avatar_path && Storage::disk('public')->exists($profile->avatar_path)) {
                 // Avoid deleting if somehow the path is the same (shouldn't happen with unique names)
                 if ($profile->avatar_path !== $newPath) {
                      Storage::disk('public')->delete($profile->avatar_path);
                 }
            }

            // Update the profile record with the path to the new avatar
            // We save it separately here after ensuring the old file is deleted
            $profile->avatar_path = $newPath;
            $profile->save();
        }


        // --- 4. Handle Role-Specific Data ---
        if ($user->hasRole('Student')) {
            $studentData = $request->safe()->only(['student_id_number', 'enrollment_date', 'major']);
            // Use updateOrCreate for student details
            $user->studentDetail()->updateOrCreate(
                ['user_id' => $user->id],
                $studentData
            );
        } elseif ($user->hasRole('Teacher')) {
            $teacherData = $request->safe()->only(['employee_id_number', 'qualification', 'department']);
            // Use updateOrCreate for teacher details
            $user->teacherDetail()->updateOrCreate(
                ['user_id' => $user->id],
                $teacherData
            );
        }
        if ($user->hasRole('Teacher')) {
            // Get validated skills (might be null if not submitted and not required)
            $skillIds = $request->safe()->input('skills', []); // Default to empty array if not present
            $user->skills()->sync($skillIds); // sync() handles adding/removing from pivot table
        }


        // --- 5. Redirect Back ---
        // Redirect back to the profile edit page with a success status message
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     * (This method is usually provided by Breeze)
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Note: Deleting the user should cascade delete related profiles,
        // student_details, teacher_details, enrollments due to onDelete('cascade')
        // in migrations. Courses created by a teacher might also be deleted (verify cascade).
        // However, **uploaded avatar files in storage/app/public/avatars ARE NOT deleted automatically.**
        // Adding logic here to delete $user->profile->avatar_path from Storage would be a good enhancement.

        $user->delete();


        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/'); // Redirect to homepage after deletion
    }
}