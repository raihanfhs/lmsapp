<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Skill; // Import the Skill model
use Illuminate\Support\Str; // For slugs

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            'Python Programming',
            'Artificial Intelligence',
            'Machine Learning',
            'Data Analyst',
            'Data Scientist',
            'Web Development (PHP/Laravel)',
            'Web Development (JavaScript/React)',
            'Cybersecurity',
            'Cloud Computing (AWS, Azure, GCP)',
            'Digital Marketing Strategy',
            'Project Management',
            'Business Analytics',
            'UI/UX',
        ];

        foreach ($skills as $skillName) {
            Skill::firstOrCreate([ // Use firstOrCreate to avoid duplicates if re-seeding
                'name' => $skillName,
                'slug' => Str::slug($skillName), // Auto-generate slug
            ]);
        }
    }
}