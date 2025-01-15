<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition()
    {
        return [
            'title'               => $this->faker->sentence(),
            'description'         => $this->faker->paragraph(),
            'start_register_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'end_register_date'   => $this->faker->dateTimeBetween('+2 weeks', '+3 weeks'),
            'start_date'          => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'end_date'            => $this->faker->dateTimeBetween('+3 months', '+4 months'),
            'status'              => $this->faker->randomElement(['Open','Closed']),
            'course_duration'     => $this->faker->numberBetween(10, 100),
            'category_id'         => Category::factory(),
            'teacher_id'          => Teacher::factory(),
        ];
    }
}
