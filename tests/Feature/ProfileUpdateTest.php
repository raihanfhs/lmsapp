<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\StudentDetail;
use Database\Seeders\RoleSeeder; // Need roles if logic depends on them
use Illuminate\Foundation\Testing\RefreshDatabase;
// Remove if not using file uploads yet: use Illuminate\Http\UploadedFile;
// Remove if not using storage assertions yet: use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase // Extend base TestCase
{
    use RefreshDatabase; // Use trait inside class

    /**
     * Seed roles before each test.
     */
     protected function setUp(): void
     {
        parent::setUp();
        $this->seed(RoleSeeder::class); // Seed roles
     }

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Student'); // Assign any role

        $this->actingAs($user)
             ->get('/profile')
             ->assertOk()
             ->assertSee($user->name)
             ->assertSee($user->email);
    }

    public function test_user_can_update_profile_information(): void
    {
        // Ensure factories exist for Profile (and others if needed)
        // Run: php artisan make:factory ProfileFactory --model=Profile if needed
        $user = User::factory()->create();
        $user->assignRole('Student');
        Profile::factory()->create(['user_id' => $user->id]); // Ensure profile exists

        $updateData = [
            'name' => 'Test User Updated',
            'email' => 'test.updated@example.com',
            'bio' => 'Updated bio here.',
            'phone_number' => '1234567890',
            'address_line_1' => '123 Test St',
            'address_line_2' => 'Apt 4B',
            'city' => 'Testville',
            'state' => 'TS',
            'postal_code' => '12345',
            'country' => 'Testland',
            'student_id_number' => 'S'.rand(10000,99999), // Add a dummy student ID
            'enrollment_date' => now()->subYear()->toDateString(), // Add a dummy date
            'major' => 'Testing',
        ];

        $this->actingAs($user)
             ->patch('/profile', $updateData)
             ->assertSessionHasNoErrors()
             ->assertRedirect('/profile');

        $user->refresh()->load('profile');

        $this->assertEquals($updateData['name'], $user->name);
        $this->assertEquals($updateData['email'], $user->email);
        $this->assertNull($user->email_verified_at); // Email verification should reset

        $this->assertNotNull($user->profile);
        $this->assertEquals($updateData['bio'], $user->profile->bio);
        $this->assertEquals($updateData['phone_number'], $user->profile->phone_number);
        $this->assertEquals($updateData['address_line_1'], $user->profile->address_line_1);
        $this->assertEquals($updateData['city'], $user->profile->city);
        // ... assert other address fields ...
    }

     public function test_student_can_update_student_specific_details(): void
     {
         // Ensure factories exist for Profile & StudentDetail
         // Run: php artisan make:factory StudentDetailFactory --model=StudentDetail if needed
         $student = User::factory()->create();
         $student->assignRole('Student');
         Profile::factory()->create(['user_id' => $student->id]);
         StudentDetail::factory()->create(['user_id' => $student->id]); // Create initial detail

         $updateData = [
             'name' => $student->name,
             'email' => $student->email,
             'student_id_number' => 'S99999',
             'enrollment_date' => '2025-01-15',
             'major' => 'Astrophysics',
         ];

         $this->actingAs($student)
              ->patch('/profile', $updateData)
              ->assertSessionHasNoErrors()
              ->assertRedirect('/profile');

         $student->refresh()->load('studentDetail');

         $this->assertNotNull($student->studentDetail);
         $this->assertEquals('S99999', $student->studentDetail->student_id_number);
         $this->assertEquals('2025-01-15', $student->studentDetail->enrollment_date);
         $this->assertEquals('Astrophysics', $student->studentDetail->major);
     }

    // Add other tests using public function test_...() format
}