<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),

            'status' => $this->faker->randomElement(['Complete', 'UnComplete']),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 week'),

          'course_id' =>  Course::factory(),
'notes' => $this->faker->text(),
        ];
    }
}
