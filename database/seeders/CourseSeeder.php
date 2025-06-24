<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('email', 'teacher@lms.test')->first();
        $student = User::where('email', 'student@lms.test')->first();
        // You might want to find the Pengelola user if they are the ones "creating" courses now
        // $pengelola = User::where('email', 'pengelola@lms.test')->first();

        // Create Course 1 (No user_id directly on courses table now)
        $course1 = Course::create([
            // 'user_id' => $teacher->id, // <-- REMOVE THIS LINE
            'title' => 'Introduction to Web Development',
            'description' => 'Learn the fundamentals of HTML, CSS, and JavaScript.',
            'course_code' => 'WEB101',
            // Add new course fields with default/sample values if needed
            'duration_months' => 3,
            'passing_grade' => 70,
            // 'final_exam_date' => now()->addMonths(3), // Example
        ]);

        // Create Course 2
        $course2 = Course::create([
            // 'user_id' => $teacher->id, // <-- REMOVE THIS LINE
            'title' => 'Laravel Basics',
            'description' => 'Get started with the Laravel framework for PHP.',
            'course_code' => 'LRVL101',
            'duration_months' => 2,
            'passing_grade' => 65,
        ]);

        // If you want to assign the seeded Teacher to these courses:
        if ($teacher && $course1) {
            // Option 1: Using Eloquent relationship (if User model has teachingCourses() defined)
            // $teacher->teachingCourses()->attach($course1->id);

            // Option 2: Direct DB insert into the pivot table
            DB::table('course_teacher')->insert([
                'user_id' => $teacher->id,
                'course_id' => $course1->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if ($teacher && $course2) {
            // $teacher->teachingCourses()->attach($course2->id);
            DB::table('course_teacher')->insert([
                'user_id' => $teacher->id,
                'course_id' => $course2->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }


        // Enroll the student user in Course 1 (only if student exists)
        if ($student && $course1) {
            Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course1->id,
            ]);
        }

        if (!$teacher) {
             $this->command->warn('   Teacher user (teacher@lms.test) not found. Course assignment to teacher skipped.');
        }
    }
}