<?php

namespace Database\Factories;

use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professor>
 */
class ProfessorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $titles = count(Title::all()->toArray());

        return [
            'name' => fake()->firstName(),
            'firstname' => fake()->lastName(),
            'title_id' => random_int(1, $titles)
        ];
    }
}
