<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Material::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'file_path' => '/materials/files/' . $this->faker->word . '.pdf',
            'video_path' => '/materials/videos/' . $this->faker->word . '.mp4',
            'course_id' => 1, 
        ];
    }
}
