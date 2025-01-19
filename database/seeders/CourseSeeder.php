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
            'title' => 'Full Stack Web Development',
            'description' => 'Master front-end and back-end development to build dynamic web applications.',
            'start_register_date' => '2024-01-01',
            'end_register_date' => '2024-01-15',
            'start_date' => '2024-02-01',
            'end_date' => '2024-05-01',
            'course_duration' => 16,
            'status' => 'Open',
            'teacher_id' => 1,
            'category_id' => 2,

        ]);

        Course::create([
            'title' => 'Machine Learning Basics',
            'description' => 'Understand the core concepts of machine learning and build simple predictive models.',
            'start_register_date' => '2024-03-01',
            'end_register_date' => '2024-03-15',
            'start_date' => '2024-04-01',
            'end_date' => '2024-07-01',

            'course_duration' => 10,
            'status' => 'Closed',
            'teacher_id' => 1,
            'category_id' => 2,

        ]);

        Course::create([
            'title' => 'UI/UX Design Principles',
            'description' => 'Learn the essentials of user interface and user experience design.',
            'start_register_date' => '2024-05-01',
            'end_register_date' => '2024-05-20',

            'start_date' => '2024-10-01',
            'end_date' => '2024-12-01',
            'course_duration' => 12,
            'status' => 'Open',
            'teacher_id' => 1,
            'category_id' => 3,
        ]); 
        Course::create([
            'title' => 'Advanced JavaScript',
            'description' => 'Deep dive into advanced JavaScript concepts, including ES6, asynchronous programming, and frameworks.',
            'start_register_date' => '2024-05-01',
            'end_register_date' => '2024-05-20',
            'start_date' => '2024-01-01',
            'end_date' => '2024-04-01',
            'course_duration' => 5,
            'status' => 'Open',
            'teacher_id' => 1,
            'category_id' => 1,
        ]);  
        Course::create([
            'title' => 'Graphic Design Mastery',
            'description' => 'Enhance your skills in Adobe Photoshop, Illustrator, and InDesign.',
            'start_register_date' => '2024-05-01',
            'end_register_date' => '2024-05-20',
            'start_date' => '2024-01-01',
            'end_date' => '2024-04-01',
            'course_duration' => 5,
            'status' => 'Open',
            'teacher_id' => 1,
            'category_id' => 3,
        ]);
        Course::create([
            'title' => 'Data Visualization with Python',
            'description' => 'Explore libraries like Matplotlib and Seaborn to create stunning data visualizations.',
            'start_register_date' => '2024-05-01',
            'end_register_date' => '2024-05-20',
            'start_date' => '2024-01-01',
            'end_date' => '2024-04-01',
            'course_duration' => 5,
            'status' => 'Open',
            'teacher_id' => 1,
            'category_id' => 2,
        ]);








    }
}
