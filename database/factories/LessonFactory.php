<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->jobTitle(),
            'user_id'=>1,
            'course_id'=>2,
            'description' => fake()->text(),
            'code' => fake()->word(),
            'visibility'=>1,
            'url_video' => fake()->url(),
            'status' => 1,
        ];
    }
}
