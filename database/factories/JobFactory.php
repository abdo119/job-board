<?php

// database/factories/JobFactory.php
namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        return [
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraphs(3, true),
            'company_name' => $this->faker->company,
            'salary_min' => $this->faker->numberBetween(50000, 100000),
            'salary_max' => $this->faker->numberBetween(100000, 200000),
            'is_remote' => $this->faker->boolean,
            'job_type' => $this->faker->randomElement(['full-time', 'part-time', 'contract']),
            'status' => 'published',
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
