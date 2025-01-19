<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'           => $this->faker->name(),
            'email'          => $this->faker->unique()->safeEmail(),
            'password'       => bcrypt('password'),
            'specialization' => $this->faker->randomElement(['Math', 'Science', 'History', 'English']), // إضافة الاختصاص
        ];
    }
}
