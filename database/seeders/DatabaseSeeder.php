<?php

namespace Database\Seeders;

use App\Models\Chantier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('make:filament-user', [
            '--name' => 'admin',
            '--email' => 'admin@admin.com',
            '--password' => 'admin',
        ]);

        Chantier::create([
            'name' => 'Atelier',
            'adresse' => config('services.google.depot_address'),
            'distance_km' => 0,
            'is_active' => true,
        ]);
    }
}
