<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'title' => 'Course 1',
            'description' => 'This is the description for Course 1',
            'start_register_date' => '2024-01-01',
            'end_register_date' => '2024-01-15',
            'start_date' => '2024-02-01',
            'end_date' => '2024-05-01',
            'status' => 'Open',
            'teacher_id' => 1,
            //'category_id' => 2,
        ]);

        Course::create([
            'title' => 'Course 2',
            'description' => 'This is the description for Course 2',
            'start_register_date' => '2024-03-01',
            'end_register_date' => '2024-03-15',
            'start_date' => '2024-04-01',
            'end_date' => '2024-07-01',
            'status' => 'Closed',
            'teacher_id' => 1,
           // 'category_id' => 3,
        ]);

        Course::create([
            'title' => 'Course 3',
            'description' => 'This is the description for Course 3',
            'start_register_date' => '2024-05-01',
            'end_register_date' => '2024-05-20',
            'start_date' => '2024-06-01',
            'end_date' => '2024-09-01',
            'status' => 'Open',
            'teacher_id' => 1,
            //'category_id' => 1,
        ]);  

    }
}
