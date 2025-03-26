<?php

// database/factories/LocationFactory.php
namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition()
    {
        return [
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->country,
        ];
    }

    // Special case for Remote locations
    public function remote(): LocationFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'city' => 'Remote',
                'state' => null,
                'country' => 'Remote',
            ];
        });
    }
}
