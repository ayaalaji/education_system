<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::create([
            'title' => 'Complete Chapter 1',
            'due_date' => '2025-01-10',
            'status' => 'Complete',
            'course_id' => 1,
        ]);

        Task::create([
            'title' => 'Submit Assignment 2',
            'due_date' => '2025-01-12',
            'status' => 'UnComplete',
            'course_id' => 2,
        ]);

        Task::create([
            'title' => 'Prepare for Quiz 3',
            'due_date' => '2025-01-15',
            'status' => 'UnComplete',
            'course_id' => 1,
        ]);

        Task::create([
            'title' => 'Project Presentation',
            'due_date' => '2025-01-18',
            'status' => 'UnComplete',
            'course_id' => 3,
        ]);

        Task::create([
            'title' => 'Read Research Paper',
            'due_date' => '2025-01-20',
            'status' => 'Complete',
            'course_id' => 2,
        ]);
    }
}
