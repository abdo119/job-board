<?php

// database/factories/AttributeFactory.php
namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'type' => $this->faker->randomElement(['number', 'text', 'boolean', 'date']),
        ];
    }
}
