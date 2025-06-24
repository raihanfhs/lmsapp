<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Models\Profile;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\Division;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules; // <-- ADD THIS LINE
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Requests\Admin\StoreUserRequest; 

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View // Added View return type hint
    {
        $users = \App\Models\User::withTrashed()->with(['roles', 'division'])->latest()->paginate(15);
        // ... rest of the method remains the same ...
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'id');
        $divisions = Division::orderBy('name')->pluck('name', 'id'); // <-- ADD THIS
        return view('admin.users.create', compact('roles', 'divisions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse // Replace Request later with StoreUserRequest
    {
        // TODO: Move validation to a Form Request (StoreUserRequest)
        $validated = $request->validated();
    
        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'division_id' => $validated['division_id'] ?? null,
        ]);

        // --- Handle Activation Preference ---  MODIFY THIS SECTION ---
        if ($validated['activation_preference'] === 'activate_now') {
            $user->email_verified_at = now();
            // No need to fire Registered event if activating immediately unless you have other listeners for it
        } else { // 'send_verification'
            $user->email_verified_at = null; // Ensure it's null
            event(new Registered($user)); // Fire event to send verification email
        }
        $user->save(); // Save the user model after potentially setting email_verified_at
        // --- End Activation Preference Handling ---
        
        $roleIds = array_map('intval', $validated['roles']);
        $user->assignRole($roleIds);
        
        // Create associated profile and role-specific details
        $assignedRoleIds = $validated['roles'];
        $studentRoleId = Role::where('name', 'Student')->first()?->id;
        $teacherRoleId = Role::where('name', 'Teacher')->first()?->id;
    
        Profile::create(['user_id' => $user->id]); // Create profile for everyone
    
        if ($studentRoleId && in_array($studentRoleId, $assignedRoleIds)) {
             StudentDetail::create(['user_id' => $user->id]);
        }
        if ($teacherRoleId && in_array($teacherRoleId, $assignedRoleIds)) {
             TeacherDetail::create(['user_id' => $user->id]);
        }
    
        // Redirect back to the user list with a success message
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View // Use route-model binding
    {
        // Fetch all available roles
        $roles = Role::pluck('name', 'id');
        $divisions = Division::orderBy('name')->pluck('name', 'id');
        $user->load('roles');
    
        // Return the edit view, passing the specific user and all available roles
        // View file: resources/views/admin/users/edit.blade.php
        return view('admin.users.edit', compact('user', 'roles', 'divisions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse // Use route-model binding
    {
        // TODO: Move validation to UpdateUserRequest later
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Ensure email is unique, BUT ignore the current user's own email address
            'email' => ['required','string','lowercase','email','max:255', Rule::unique('users')->ignore($user->id)],
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'division_id' => 'nullable|integer|exists:divisions,id',
            // No password validation here as we are not changing it
        ]);
    
        // Update user's name and email
        $user->update([
             'name' => $validated['name'],
             'email' => $validated['email'],
             'division_id' => $validated['division_id'] ?? null,
        ]);
    
        // Convert role IDs to integers for robustness
        $roleIds = array_map('intval', $validated['roles']);
    
        // Sync roles - this removes roles not in the array and adds new ones
        $user->syncRoles($roleIds);
    
         // We don't need to update Profile/StudentDetail/TeacherDetail here usually,
         // but we DO need to create/delete Student/Teacher detail records if the role changes!
    
        $studentRoleId = Role::where('name', 'Student')->first()?->id;
        $teacherRoleId = Role::where('name', 'Teacher')->first()?->id;
    
        // If Student role added, ensure detail record exists
        if ($studentRoleId && in_array($studentRoleId, $roleIds)) {
             $user->studentDetail()->firstOrCreate(['user_id' => $user->id]);
        } else {
            // If Student role removed, delete detail record
            $user->studentDetail?->delete();
        }
    
        // If Teacher role added, ensure detail record exists
         if ($teacherRoleId && in_array($teacherRoleId, $roleIds)) {
             $user->teacherDetail()->firstOrCreate(['user_id' => $user->id]);
        } else {
            // If Teacher role removed, delete detail record
            $user->teacherDetail?->delete();
        }
    
    
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
    
    public function activate(string $id): RedirectResponse // Use string $id as trashed models aren't resolved by default
    {
        // Find the user, including those that are soft-deleted
        $user = User::withTrashed()->findOrFail($id);

        // Ensure we are not trying to activate an already active user (optional check)
        if (!$user->trashed()) {
            return redirect()->route('admin.users.index')
                            ->with('error', "User '{$user->name}' is already active.");
        }

        $user->restore(); // This removes the 'deleted_at' timestamp

        return redirect()->route('admin.users.index')
                        ->with('success', "User '{$user->name}' activated successfully.");
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse 
    {

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'You cannot delete your own account.');
        }
        $userName = $user->name;
        $user->delete();
    
        // Redirect back to the user list with a success message
        return redirect()->route('admin.users.index')
                         ->with('success', "User '{$userName}' deactivated successfully.");

    }
}
