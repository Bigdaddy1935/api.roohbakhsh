<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->streetName(),
            'user_id'=>1,
            'category_id'=>1,
            'description' => fake()->text(),
            'code' => fake()->word(),
            'visibility'=>1,
            'status' => 1,
            'image_id'=>5,


        ];
    }
}
