<?php

namespace Database\Factories;

use App\Models\Jurisdiction;
use App\Models\County;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class JurisdictionFactory extends Factory
{
    protected $model = Jurisdiction::class;

    public function definition()
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->city,
            'county_id' => County::factory(), // Ensure this points to a valid county
            // Add other fields as necessary
        ];
    }
}
