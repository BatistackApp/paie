<?php

namespace App\Service;

use App\Enums\AbsenceType;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class UserStatsService
{
    protected array $cache = [];

    public function __construct(
        protected CcnCalculatorService $calculator,
        protected OvertimeCalculatorService $overtimeCalculator,
        protected AbsenceService $absenceService,
    ) {}

    /**
     * Calcule les statistiques globales ou mensuelles pour un salarié.
     */
    public function getStatsForUser(User $user, ?CarbonInterface $month = null): array
    {
        $cacheKey = "user_{$user->id}_".($month ? $month->format('Y-m') : 'total');

        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        // --- OPTIMISATION SQL ---
        $allEntries = $user->timeEntries()
            ->select(['id', 'user_id', 'chantier_id', 'entry_date', 'work_duration', 'travel_duration'])
            ->with('chantier:id,distance_km')
            ->get();
        $allAbsences = $user->absences()
            ->where('is_validated', true)
            ->get();

        $now = now();
        $targetMonth = $month ?? $now;
        $contract = (float) $user->weekly_contract_hours;
        $hoursPerDay = $contract / 5; // Estimation du volume horaire d'une journée d'absence

        // --- LOGIQUE CONGÉS PAYÉS (CP) ---
        // On utilise hired_at, sinon created_at par défaut
        $referenceDate = $user->hired_at ?? $user->created_at;

        // Calcul du nombre de mois entiers depuis l'embauche
        $monthsWorked = $referenceDate->diffInMonths($now);

        // Dans le BTP, on acquiert souvent 2.5 jours par mois de travail effectif
        $cpAcquired = round($monthsWorked * 2.5, 2);

        // Calcul du consommé (Absences de type CP validées)
        $cpTaken = $allAbsences->where('absence_type', AbsenceType::CONGE_PAYE)
            ->sum(fn($a) => $this->absenceService->calculateAbsenceDays($a));

        $cpBalance = $cpAcquired - $cpTaken;

        $monthEntries = $allEntries->filter(fn($e) =>
            $e->entry_date->month === $targetMonth->month && $e->entry_date->year === $targetMonth->year
        );

        // --- Gestion du Repos Compensateur (RC) ---
        // On récupère les heures de repos compensateur prises sur le mois
        $rcHoursTaken = $user->absences()
            ->where('absence_type', AbsenceType::REPOS_COMPENSATEUR)
            ->where(function ($query) use ($targetMonth) {
                $query->whereMonth('start_date', $targetMonth->month)
                    ->whereYear('start_date', $targetMonth->year);
            })
            ->get()
            ->sum(function ($absence) use ($hoursPerDay) {
                return $this->absenceService->calculateAbsenceDays($absence) * $hoursPerDay;
            });

        // --- Calcul des HS brutes du mois ---
        $weeklyGroups = $monthEntries->groupBy(fn ($e) => $e->entry_date->format('Y-W'));
        $rawExtra25 = 0;
        $rawExtra50 = 0;

        foreach ($weeklyGroups as $weekEntries) {
            $totalWeek = $weekEntries->sum('work_duration');
            $over = max(0, $totalWeek - $contract);
            $rawExtra25 += min($over, 8.0);
            $rawExtra50 += max(0, $over - 8.0);
        }

        // --- Application de la déduction RC ---
        // On déduit en priorité des heures à 50% puis des 25%
        $remainingRC = $rcHoursTaken;

        $netExtra50 = max(0, $rawExtra50 - $remainingRC);
        $remainingRC = max(0, $remainingRC - $rawExtra50);

        $netExtra25 = max(0, $rawExtra25 - $remainingRC);

        // --- Cumuls historiques ---
        $totalWork = $allEntries->sum('work_duration');

        $totalExtra25 = 0;
        if (! $month) {
            $totalExtra25 = $allEntries->groupBy(fn ($e) => $e->entry_date->format('Y-W'))
                ->map(function (Collection $week) use ($contract) {
                    return min(max(0, $week->sum('work_duration') - $contract), 8.0);
                })->sum();
        }

        $result = [
            'work_hours' => round($monthEntries->sum('work_duration'), 2),
            'travel_hours' => round($monthEntries->sum('travel_duration'), 2),
            'gd_count' => $monthEntries->filter(fn ($e) => ($e->chantier->distance_km ?? 0) > 50)->count(),
            'rc_hours_deducted' => round($rcHoursTaken, 2),
            'extra_25' => round($netExtra25, 2),
            'extra_50' => round($netExtra50, 2),
            'total_work' => round($totalWork, 2),
            'total_extra_25' => round($totalExtra25, 2),
            'cp_acquired' => $cpAcquired,
            'cp_taken' => $cpTaken,
            'cp_balance' => $cpBalance,
            'hired_at' => $user->hired_at ? $user->hired_at->format('d/m/Y') : 'Non renseignée',
        ];

        return $this->cache[$cacheKey] = $result;
    }
}
