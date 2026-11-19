<?php

namespace Database\Factories;

use App\Models\Jurisdiction;
use App\Models\Section;
use App\Models\SectionTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'jurisdiction_id' => Jurisdiction::factory(),
            'section_title_id' => SectionTitle::factory(),
            'text' => '<p>'.fake()->sentence().'</p>',
        ];
    }
}
