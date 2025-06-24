<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;          // Import User model
use App\Models\Profile;       // Import Profile model
use App\Models\StudentDetail; // Import StudentDetail model
use App\Models\TeacherDetail; // Import TeacherDetail model
use Illuminate\Support\Facades\Hash; // Import Hash facade for passwords

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- Create Admin User ---
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@lms.test', // Email for admin login
            'password' => Hash::make('password'), // Set password to 'password' (change for production!)
            'email_verified_at' => now(), // Pre-verify email for convenience
        ]);
        // Assign the 'Admin' role (must match role created by RoleSeeder)
        $admin->assignRole('Admin');
        // Create a basic profile entry
        Profile::create(['user_id' => $admin->id, 'bio' => 'System Administrator']);
        // Admins typically don't need Student or Teacher details

        $pengelola = User::create([
            'name' => 'Pengelola User',
            'email' => 'pengelola@lms.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $pengelola->assignRole('Pengelola');
        Profile::create(['user_id' => $pengelola->id, 'bio' => 'Course and Platform Manager']);
        // Pengelola might not need specific Student or Teacher details, just a profile.
        // --- END PENGELOLA SECTION ---

        // --- Create Teacher User ---
        $teacher = User::create([
            'name' => 'Teacher User',
            'email' => 'teacher@lms.test', // Email for teacher login
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        // Assign the 'Teacher' role
        $teacher->assignRole('Teacher');
        // Create profile entry
        Profile::create(['user_id' => $teacher->id, 'bio' => 'Experienced Course Instructor']);
        // Create teacher detail entry (can add details later via profile page)
        TeacherDetail::create(['user_id' => $teacher->id]);


        // --- Create Student User ---
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student@lms.test', // Email for student login
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        // Assign the 'Student' role
        $student->assignRole('Student');
        // Create profile entry
        Profile::create(['user_id' => $student->id, 'bio' => 'Eager Learner']);
        // Create student detail entry (can add details later via profile page)
        StudentDetail::create(['user_id' => $student->id]);


        // You could add more users using factories if needed later:
        // User::factory(5)->create()->each(function($user) {
        //     $user->assignRole('Student');
        //     Profile::create(['user_id' => $user->id]);
        //     StudentDetail::create(['user_id' => $user->id]);
        // });
    }
}