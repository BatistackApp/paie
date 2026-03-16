<?php

namespace Database\Factories;

use App\Models\Advance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AdvanceFactory extends Factory
{
    protected $model = Advance::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->randomFloat(),
            'date' => Carbon::now(),
            'type' => $this->faker->word(),
            'reason' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
