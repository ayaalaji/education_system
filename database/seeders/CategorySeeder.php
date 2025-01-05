<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name'          =>  'Programming',
            'description'   => 'Courses related to programming languages and development techniques.',
            'teacher_id'    => 1
        ]);


        Category::create([
            'name'          =>  'Data Science',
            'description'   => 'Learn data analysis, machine learning, and data visualization techniques.',
            'teacher_id'    => 2
        ]);

        Category::create([
            'name'          =>  'Design',
            'description'   => 'Courses focused on graphic design, UI/UX, and creative design tools.',
            'teacher_id'    => 3
        ]);
    }
}
