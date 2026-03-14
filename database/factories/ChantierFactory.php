<?php

namespace Database\Factories;

use App\Models\Chantier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ChantierFactory extends Factory
{
    protected $model = Chantier::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'adresse' => $this->faker->word(),
            'distance_km' => $this->faker->randomFloat(),
            'is_active' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
