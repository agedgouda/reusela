<?php

namespace Database\Factories;

use App\Models\County;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CountyFactory extends Factory
{
    protected $model = County::class;

    public function definition()
    {
        return [
            'id' => Str::uuid()->toString(),
            'name' => $this->faker->state,
            // Add other fields as necessary
        ];
    }
}
