<?php

namespace App\Service;

use App\Models\Absence;
use App\Models\TimeEntry;
use Illuminate\Support\Carbon;

class AbsenceService
{
    /**
     * Vérifie si un salarié a une absence prévue à une date donnée.
     */
    public function hasAbsenceOnDate(int $userId, $date): bool
    {
        $checkDate = Carbon::parse($date)->toDateString();

        return Absence::where('user_id', $userId)
            ->whereDate('start_date', '<=', $checkDate)
            ->whereDate('end_date', '>=', $checkDate)
            ->exists();
    }

    /**
     * Calcule le nombre de jours ouvrés pour une absence.
     */
    public function calculateAbsenceDays(Absence $absence): int
    {
        $start = Carbon::parse($absence->start_date);
        $end = Carbon::parse($absence->end_date);

        // On compte les jours en excluant les week-ends (selon usage standard)
        return $start->diffInDaysFiltered(function (Carbon $date) {
            return ! $date->isWeekend();
        }, $end) + 1;
    }

    /**
     * Vérifie les conflits entre une nouvelle absence et des heures déjà saisies.
     */
    public function checkConflictWithWorkEntries(Absence $absence): bool
    {
        return TimeEntry::where('user_id', $absence->user_id)
            ->whereBetween('entry_date', [$absence->start_date, $absence->end_date])
            ->exists();
    }
}
