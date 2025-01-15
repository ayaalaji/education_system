<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'        => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'teacher_id'  => Teacher::factory(), // ربط الفئة مع أستاذ باستخدام الفاكتوري
        ];
    }
}
