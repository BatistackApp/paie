<?php

namespace App\Observers;

use App\Jobs\UpdateChantierDistanceJob;
use App\Models\Chantier;

class ChantierObserver
{
    public function saved(Chantier $chantier): void
    {
        if ($chantier->wasRecentlyCreated || $chantier->isDirty('adresse')) {
            UpdateChantierDistanceJob::dispatch($chantier);
        }
    }
}
