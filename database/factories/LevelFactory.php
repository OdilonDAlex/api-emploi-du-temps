<?php

namespace Database\Factories;

use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Level>
 */
class LevelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $count = count(ClassRoom::all()->toArray());
        return [
            'name' => fake()->randomLetter(),
            'studentsNumber' => random_int(20, 200),
            'classroom_id' => random_int(1, $count)
        ];
    }
}
