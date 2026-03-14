<?php

namespace Database\Factories;

use App\Models\Chantier;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition()
    {
        return [
            'entry_date' => Carbon::now(),
            'depart_depot' => Carbon::now(),
            'embauche_chantier' => Carbon::now(),
            'debauche_chantier' => Carbon::now(),
            'retour_depot' => Carbon::now(),
            'break_duration_minute' => $this->faker->randomNumber(),
            'has_meal' => $this->faker->boolean(),
            'has_night' => $this->faker->boolean(),
            'is_validated' => $this->faker->boolean(),
            'validated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'chantier_id' => Chantier::factory(),
        ];
    }
}
