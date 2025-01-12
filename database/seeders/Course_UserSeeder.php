<?php

namespace Database\Seeders;

use App\Models\CourseUser;
use App\Models\Course_User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class Course_UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseUser::create([
            'course_id'  => 1,
            'user_id'    => 1
        ]);

        CourseUser::create([
            'course_id'  => 1,
            'user_id'    => 2
        ]);

        CourseUser::create([
            'course_id'  => 2,
            'user_id'    => 1
        ]);

        CourseUser::create([
            'course_id'  => 3,
            'user_id'    => 2
        ]);

        CourseUser::create([
            'course_id'  => 4,
            'user_id'    => 3
        ]);
    }
}
