<?php

namespace App\Service;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class UserStatsService
{
    protected array $cache = [];

    public function __construct(
        protected CcnCalculatorService $calculator,
        protected OvertimeCalculatorService $overtimeCalculator
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

        // On charge les données calculées (work_duration et travel_duration sont déjà en base)
        $allEntries = $user->timeEntries()
            ->select(['id', 'user_id', 'chantier_id', 'entry_date', 'work_duration', 'travel_duration'])
            ->with('chantier:id,distance_km')
            ->get();

        $now = now();
        $targetMonth = $month ?? $now;
        $contract = (float) $user->weekly_contract_hours;

        // --- Stats du mois (Filtrage simple en mémoire) ---
        $monthEntries = $allEntries->filter(fn ($e) => $e->entry_date->month === $targetMonth->month &&
            $e->entry_date->year === $targetMonth->year
        );

        // --- Calcul des HS du mois (Basé sur work_duration pré-calculé) ---
        $weeklyGroups = $monthEntries->groupBy(fn ($e) => $e->entry_date->format('Y-W'));
        $extra25 = 0;
        $extra50 = 0;

        foreach ($weeklyGroups as $weekEntries) {
            $totalWeek = $weekEntries->sum('work_duration');
            $over = max(0, $totalWeek - $contract);
            $extra25 += min($over, 8.0);
            $extra50 += max(0, $over - 8.0);
        }

        // --- Cumuls historiques (Sommes SQL directes sur la collection) ---
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
            'extra_25' => round($extra25, 2),
            'extra_50' => round($extra50, 2),
            'total_work' => round($totalWork, 2),
            'total_extra_25' => round($totalExtra25, 2),
        ];

        return $this->cache[$cacheKey] = $result;
    }
}
