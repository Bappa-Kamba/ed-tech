<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lesson;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
c
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@ed-tech.com',
            'role' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@ed-tech.com',
            'role' => 'student',
        ]);

        Lesson::factory(3)->create([
            'title' => 'Introduction to Laravel',
            'created_by' => User::where('email', 'admin@ed-tech.com')->first()->id,
        ]);
    }
}
