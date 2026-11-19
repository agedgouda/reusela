<?php

namespace Database\Factories;

use App\Models\SectionTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SectionTitle>
 */
class SectionTitleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(2, true),
            'sort_order' => fake()->numberBetween(1, 100),
            'icon' => null,
        ];
    }
}
