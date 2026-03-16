<?php

namespace App\Observers;

use App\Models\TimeEntry;
use App\Service\AbsenceService;
use App\Service\CcnCalculatorService;
use Illuminate\Validation\ValidationException;

class TimeEntryObserver
{
    public function __construct(protected AbsenceService $absenceService, protected CcnCalculatorService $calculator) {}

    public function creating(TimeEntry $timeEntry): void
    {
        if ($this->absenceService->hasAbsenceOnDate($timeEntry->user_id, $timeEntry->entry_date)) {
            throw ValidationException::withMessages([
                'entry_date' => 'Le salarié est déclaré absent à cette date. Impossible de saisir des heures.',
            ]);
        }
    }

    public function saving(TimeEntry $timeEntry): void
    {
        // On calcule une seule fois au moment de la saisie
        $timeEntry->work_duration = $this->calculator->calculateWorkDuration($timeEntry);
        $timeEntry->travel_duration = $this->calculator->calculateTravelDuration($timeEntry);
    }
}
