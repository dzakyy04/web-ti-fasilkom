<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $title = $this->faker->sentence(10, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title, '-'),
            'thumbnail' => 'https://source.unsplash.com/random/800x600/?news',
            'content' => $this->faker->paragraphs(5, true),
            'type' => $this->faker->randomElement(['news', 'announcement']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
