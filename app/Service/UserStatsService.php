<?php

namespace App\Service;

use App\Enums\AbsenceType;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class UserStatsService
{
    /**
     * Cache statique pour éviter de recalculer les stats d'un même utilisateur
     * plusieurs fois durant le cycle de vie d'une seule requête.
     */
    protected static array $requestCache = [];

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
        $targetMonth = $month ?? now();
        $cacheKey = "user_{$user->id}_{$targetMonth->format('Y-m')}";

        if (isset(self::$requestCache[$cacheKey])) {
            return self::$requestCache[$cacheKey];
        }

        // --- CHARGEMENT UNIQUE ET CIBLÉ ---
        $allEntries = $user->timeEntries()
            ->select(['id', 'user_id', 'chantier_id', 'entry_date', 'work_duration', 'travel_duration'])
            ->with('chantier:id,distance_km')
            ->get();

        $allAbsences = $user->absences()->where('is_validated', true)->get();

        $contract = (float) ($user->weekly_contract_hours ?? 35.0);
        $year = $targetMonth->year;

        // --- STATS DU MOIS CIBLE ---
        $monthEntries = $allEntries->filter(fn($e) =>
            $e->entry_date->month === $targetMonth->month && $e->entry_date->year === $year
        );

        // Heures Supplémentaires Mensuelles
        $monthlyHS = $monthEntries->groupBy(fn($e) => $e->entry_date->format('W'))
            ->map(function(Collection $week) use ($contract) {
                $over = max(0, $week->sum('work_duration') - $contract);
                return ['h25' => min($over, 8), 'h50' => max(0, $over - 8)];
            });

        // --- STATS RH / CUMULS ---
        // Contingent HS Annuel
        $annualOvertimeHours = $allEntries
            ->filter(fn($e) => $e->entry_date->year === $year)
            ->groupBy(fn($e) => $e->entry_date->format('W'))
            ->map(fn(Collection $week) => max(0, $week->sum('work_duration') - $contract))
            ->sum();

        // Congés Payés
        $referenceDate = $user->hired_at ?? $user->created_at;
        $monthsWorked = max(0, $referenceDate->diffInMonths(now()));
        $cpAcquired = round($monthsWorked * 2.5, 2);
        $cpTaken = $allAbsences->where('absence_type', AbsenceType::CONGE_PAYE)
            ->sum(fn($a) => $this->absenceService->calculateAbsenceDays($a));

        return self::$requestCache[$cacheKey] = [
            // Stats du Mois
            'month_work' => round($monthEntries->sum('work_duration'), 2),
            'month_travel' => round($monthEntries->sum('travel_duration'), 2),
            'month_extra_25' => round($monthlyHS->sum('h25'), 2),
            'month_extra_50' => round($monthlyHS->sum('h50'), 2),
            'month_gd_count' => $monthEntries->filter(fn($e) => ($e->chantier->distance_km ?? 0) > 50)->count(),

            // Stats Annuelles / Globales
            'cp_balance' => round(($user->cp_carry_over ?? 0) + $cpAcquired - $cpTaken, 2),
            'annual_overtime' => round($annualOvertimeHours, 2),
            'total_gd_count' => $allEntries->filter(fn($e) => ($e->chantier->distance_km ?? 0) > 50)->count(),
            'contingent_percent' => min(100, round(($annualOvertimeHours / 220) * 100, 1)),
        ];
    }
}
