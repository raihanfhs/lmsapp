<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Division; // Import the Division model

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define some initial internal company divisions
        $divisions = [
            ['name' => 'System Administration', 'description' => 'Manages overall system users and core settings.'],
            ['name' => 'Course Operations', 'description' => 'Manages the course catalog, teacher assignments, and general course administration.'],
            ['name' => 'Tech Content Development', 'description' => 'Team responsible for developing and teaching technology-related courses.'],
            ['name' => 'Business Content Development', 'description' => 'Team responsible for developing and teaching business and marketing courses.'],
            ['name' => 'Student Support', 'description' => 'Handles student inquiries and support.'],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}