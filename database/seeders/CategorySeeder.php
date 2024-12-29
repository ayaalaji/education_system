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
            'name'          =>  'cat 1',
            'description'   => 'this is category one',
            'teacher_id'    => 1
        ]);


        Category::create([
            'name'          =>  'cat 2',
            'description'   => 'this is category two',
            'teacher_id'    => 2
        ]);

        Category::create([
            'name'          =>  'cat 3',
            'description'   => 'this is category two',
            'teacher_id'    => 3
        ]);
    }
}
