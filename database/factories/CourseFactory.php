<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subjects = count(Subject::all()->toArray());
        return [
            'duration' => 4,
            'subject_id' => random_int(1, $subjects),
            'weekOf' => '2025-03-08',
            'user_id' => 1
        ];
    }
}
