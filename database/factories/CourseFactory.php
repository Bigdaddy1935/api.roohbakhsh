<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->title(),
            'user_id'=>1,
            'category_id'=>1,
            'description' => fake()->text(),
            'code' => fake()->word(),
            'visibility'=>1,
            'access' => 1,
            'status' => 1,
            'image_id'=>2,
            'navigation'=>1,
        ];
    }
}
