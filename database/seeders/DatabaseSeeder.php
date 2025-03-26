<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Language;
use App\Models\Location;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create 5 languages
        $languages = Language::factory()->createMany([
            ['name' => 'PHP'],
            ['name' => 'JavaScript'],
            ['name' => 'Python'],
            ['name' => 'Java'],
            ['name' => 'Go']
        ]);

        // Create 4 locations
        $locations = Location::factory()->createMany([
            ['city' => 'New York', 'state' => 'NY', 'country' => 'USA'],
            Location::factory()->remote()->make()->toArray(), // For remote location
            ['city' => 'London', 'state' => null, 'country' => 'UK'],
            ['city' => 'Berlin', 'state' => null, 'country' => 'Germany'],
        ]);


        // Create 4 categories
        $categories = Category::factory()->createMany([
            ['name' => 'Web Development'],
            ['name' => 'Mobile Development'],
            ['name' => 'DevOps'],
            ['name' => 'Data Science']
        ]);

        // Create 3 attributes
        $attributes = Attribute::factory()->createMany([
            ['name' => 'years_experience', 'type' => 'number'],
            ['name' => 'education_level', 'type' => 'text'],
            ['name' => 'employment_type', 'type' => 'text']
        ]);

        // Create 20 jobs with relationships
        Job::factory(20)->create()->each(function ($job) use ($languages, $locations, $categories, $attributes) {
            $job->languages()->attach($languages->random(rand(1, 3))->pluck('id'));
            $job->locations()->attach($locations->random(rand(1, 2))->pluck('id'));
            $job->categories()->attach($categories->random(rand(1, 2))->pluck('id'));

            // Add attribute values
            $job->attributeValues()->create([
                'attribute_id' => $attributes->firstWhere('name', 'years_experience')->id,
                'value' => rand(1, 10)
            ]);
        });
    }
}
