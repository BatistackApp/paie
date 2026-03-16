<?php

namespace App\Service;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class OvertimeCalculatorService
{
    public function __construct(
        protected CcnCalculatorService $ccnCalculator
    ) {}

    /**
     * Analyse une semaine spécifique pour un utilisateur et ventile les heures.
     */
    public function calculateWeeklyOvertime(User $user, int $year, int $weekNumber): array
    {
        // On définit les dates de début et de fin de la semaine ISO pour optimiser la requête SQL
        $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Récupération des entrées uniquement pour la période concernée (plus performant que de filtrer en PHP)
        $entries = $user->timeEntries()
            ->whereBetween('entry_date', [$startOfWeek, $endOfWeek])
            ->get();

        // Correction de l'erreur : on map d'abord les durées avant de sommer les nombres
        // Cela évite de passer une Closure à la méthode sum() qui peut être confondue avec celle du Builder
        $totalEffectiveWork = $entries->map(fn ($e) => $this->ccnCalculator->calculateWorkDuration($e))->sum();

        $contract = (float) $user->weekly_contract_hours;

        $normalHours = min($totalEffectiveWork, $contract);
        $remaining = max(0, $totalEffectiveWork - $contract);

        // Tranche 1 : +25% (de contract à contract + 8h, max 43h pour un 35h)
        $h25 = min($remaining, 8.0);

        // Tranche 2 : +50% (au-delà de contract + 8h)
        $h50 = max(0, $remaining - 8.0);

        return [
            'total_effective' => round($totalEffectiveWork, 2),
            'contract_base' => $contract,
            'normal' => round($normalHours, 2),
            'extra_25' => round($h25, 2),
            'extra_50' => round($h50, 2),
            'week_number' => $weekNumber,
            'year' => $year,
        ];
    }

    /**
     * Calcule le cumul des HS sur un mois complet (en agrégeant les semaines).
     */
    public function calculateMonthlyOvertime(User $user, CarbonInterface $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        $weeks = [];
        $current = $start->copy();

        while ($current <= $end) {
            $weekNum = $current->weekOfYear;
            $year = $current->year;

            $weeks[$year.'-'.$weekNum] = $this->calculateWeeklyOvertime($user, $year, $weekNum);

            // On avance d'une semaine tout en restant dans la logique ISO
            $current->addWeek()->startOfWeek();
        }

        return [
            'month' => $month->format('m/Y'),
            'total_25' => collect($weeks)->sum('extra_25'),
            'total_50' => collect($weeks)->sum('extra_50'),
            'details_per_week' => $weeks,
        ];
    }
}
