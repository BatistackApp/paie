<?php

namespace App\Observers;

use App\Models\Absence;
use App\Models\TimeEntry;
use Illuminate\Validation\ValidationException;

class AbsenceObserver
{
    public function creating(Absence $absence): void
    {
        $hasWork = TimeEntry::where('user_id', $absence->user_id)
            ->whereBetween('entry_date', [$absence->start_date, $absence->end_date])
            ->exists();

        if ($hasWork) {
            throw ValidationException::withMessages([
                'start_date' => 'Des heures de travail existent déjà sur cette période. Vérifiez les saisies.',
            ]);
        }
    }
}
