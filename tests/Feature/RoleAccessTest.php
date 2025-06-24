<?php

namespace Tests\Feature; // Standard namespace

use Illuminate\Foundation\Testing\RefreshDatabase; // Import trait
use Tests\TestCase; // Import base TestCase
use App\Models\User;
use Database\Seeders\RoleSeeder; // Import RoleSeeder

class RoleAccessTest extends TestCase // Extend Laravel's base TestCase
{
    use RefreshDatabase; // Use the trait inside the class

    /**
     * Seed the roles before each test runs.
     */
    protected function setUp(): void
    {
        parent::setUp(); // Always call parent setUp
        $this->seed(RoleSeeder::class); // Seed roles
    }

    // Convert test(...) to public function test_...(): void
    public function test_guest_cannot_access_protected_dashboards(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get(route('admin.dashboard'))->assertRedirect('/login');
        $this->get(route('teacher.dashboard'))->assertRedirect('/login');
        $this->get(route('student.dashboard'))->assertRedirect('/login');
    }

    public function test_student_cannot_access_admin_or_teacher_routes(): void
    {
        $student = User::factory()->create();
        $student->assignRole('Student');

        $this->actingAs($student)
             ->get(route('admin.dashboard'))
             ->assertForbidden();

        $this->actingAs($student)
             ->get(route('teacher.dashboard'))
             ->assertForbidden();

        $this->actingAs($student)
             ->get(route('teacher.courses.create'))
             ->assertForbidden();

        $this->actingAs($student)
             ->get(route('student.dashboard'))
             ->assertOk();
    }

    public function test_teacher_cannot_access_admin_or_student_routes(): void
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('Teacher');

        $this->actingAs($teacher)
             ->get(route('admin.dashboard'))
             ->assertForbidden();

        $this->actingAs($teacher)
             ->get(route('student.dashboard'))
             ->assertForbidden();

         $this->actingAs($teacher)
             ->get(route('student.courses.index'))
             ->assertForbidden();

        $this->actingAs($teacher)
             ->get(route('teacher.dashboard'))
             ->assertOk();

        $this->actingAs($teacher)
             ->get(route('teacher.courses.index'))
             ->assertOk();
    }

    public function test_admin_can_access_admin_dashboard(): void // Renamed test slightly for clarity
    {
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        $this->actingAs($admin)
             ->get(route('admin.dashboard'))
             ->assertOk();

        // Add asserts here later if Admin should/shouldn't access other roles' routes
    }
}