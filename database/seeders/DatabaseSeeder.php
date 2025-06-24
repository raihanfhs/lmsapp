<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Call Seeders in Order ---
        // 1. Create Roles (Admin, Teacher, Student)
        $this->call(RoleSeeder::class);
        
        $this->call(DivisionSeeder::class);
        // 2. Create sample Users for each role
        $this->call(UserSeeder::class);
        $this->call(SkillSeeder::class);
        // 3. Create sample Courses assigned to the Teacher user and enroll Student
        $this->call(CourseSeeder::class);

        // You can add calls to other seeders here later if needed

        // Comment out or remove default factory call if it exists:
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}