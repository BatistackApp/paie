<?php

namespace App\Jobs;

use App\Models\Chantier;
use App\Service\GoogleDistanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateChantierDistanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Chantier $chantier) {}

    public function handle(GoogleDistanceService $service): void
    {
        $distance = $service->getDistanceInKm($this->chantier->adresse);

        if ($distance !== null) {
            $this->chantier->updateQuietly(['distance_km' => $distance]);
        }
    }
}
