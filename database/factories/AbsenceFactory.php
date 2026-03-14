<?php

namespace Database\Factories;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AbsenceFactory extends Factory
{
    protected $model = Absence::class;

    public function definition()
    {
        return [
            'absence_type' => $this->faker->word(),
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now(),
            'comment' => $this->faker->word(),
            'is_validated' => $this->faker->boolean(),
            'validated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
        ];
    }
}
