<?php

namespace App\Observers;

use App\Models\TimeEntry;
use App\Service\AbsenceService;
use Illuminate\Validation\ValidationException;

class TimeEntryObserver
{
    public function __construct(protected AbsenceService $absenceService) {}

    public function creating(TimeEntry $timeEntry): void
    {
        if ($this->absenceService->hasAbsenceOnDate($timeEntry->user_id, $timeEntry->entry_date)) {
            throw ValidationException::withMessages([
                'entry_date' => 'Le salarié est déclaré absent à cette date. Impossible de saisir des heures.',
            ]);
        }
    }
}
