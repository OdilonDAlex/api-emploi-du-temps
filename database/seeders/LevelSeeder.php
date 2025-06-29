<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(['Licence 1', 'Licence 2', 'Licence 3', 'Master 1', 'Master 2'] as $levelName) {
            Level::factory()->create([
                'name' => $levelName,
            ]);
        }
    }
}
