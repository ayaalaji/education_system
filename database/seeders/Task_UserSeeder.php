<?php

namespace Database\Seeders;

use App\Models\TaskUser;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Task_UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskUser::create([
            'student_id' => 1,
            'task_id'    => 1
        ]);

        TaskUser::create([
            'student_id' => 1,
            'task_id'    => 2
        ]);
        TaskUser::create([
            'student_id' => 2,
            'task_id'    => 1
        ]);

        TaskUser::create([
            'student_id' => 2,
            'task_id'    => 2
        ]);
        TaskUser::create([
            'student_id' => 3,
            'task_id'    => 5
        ]);

        TaskUser::create([
            'student_id' => 4,
            'task_id'    => 4
        ]);
    }
}
