<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-6 months', '+3 months');
        $end = (clone $start)->modify('+'.rand(1,6).' months');

        return [
            'name' => $this->faker->catchPhrase,
            'description' => $this->faker->optional()->paragraph,
            'status' => $this->faker->randomElement(['planned','active','completed']),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'budget' => $this->faker->randomFloat(2, 1000, 100000),
        ];
    }
}
