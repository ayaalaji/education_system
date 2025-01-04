<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // input  data
        Material::create([
            'title' => 'Introduction to Programming',
            'file_path' => '/materials/files/intro_programming.pdf',
            'video_path' => '/materials/videos/intro_programming.mp4',
            'course_id' => 2, 
        ]);

        Material::create([
            'title' => 'Advanced Database Concepts',
            'file_path' => '/materials/files/advanced_database.pdf',
            'video_path' => '/materials/videos/advanced_database.mp4',
            'course_id' => 1, 
        ]);

        Material::factory(10)->create();

    }
}
