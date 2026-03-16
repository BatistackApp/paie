<?php

namespace App\Service;

use App\Models\User;
use Carbon\CarbonInterface;

class UserStatsService
{
    public function __construct(protected CcnCalculatorService $calculator) {}

    /**
     * Calcule les statistiques globales ou mensuelles pour un salarié.
     */
    public function getStatsForUser(User $user, ?CarbonInterface $month = null): array
    {
        // On récupère toutes les entrées avec les chantiers pour les calculs
        $allEntries = $user->timeEntries()->with('chantier')->get();

        // Définition de la période actuelle
        $now = now();

        // Filtrage des entrées selon la période demandée (scope dynamique)
        $scopedEntries = $month
            ? $allEntries->filter(fn ($e) => $e->entry_date->month === $month->month && $e->entry_date->year === $month->year)
            : $allEntries;

        // Filtrage spécifique pour le mois en cours
        $currentMonthEntries = $allEntries->filter(fn ($e) =>
            $e->entry_date->month === $now->month &&
            $e->entry_date->year === $now->year
        );

        return [
            // --- Stats du scope (Généralement utilisé pour les listes filtrées) ---
            'work_hours' => round($scopedEntries->sum(fn ($e) => $this->calculator->calculateWorkDuration($e)), 2),
            'travel_hours' => round($scopedEntries->sum(fn ($e) => $this->calculator->calculateTravelDuration($e)), 2),
            'grand_deplacement_count' => $scopedEntries->filter(fn ($e) => ($e->chantier->distance_km ?? 0) > 50)->count(),

            // --- Stats spécifiques au MOIS EN COURS ---
            'month_work_hours' => round($currentMonthEntries->sum(fn ($e) => $this->calculator->calculateWorkDuration($e)), 2),
            'month_travel_hours' => round($currentMonthEntries->sum(fn ($e) => $this->calculator->calculateTravelDuration($e)), 2),
            'month_grand_deplacement_count' => $currentMonthEntries->filter(fn ($e) => ($e->chantier->distance_km ?? 0) > 50)->count(),

            // --- Stats CUMULÉES TOTALES ---
            'total_work_hours' => round($allEntries->sum(fn ($e) => $this->calculator->calculateWorkDuration($e)), 2),
            'total_travel_hours' => round($allEntries->sum(fn ($e) => $this->calculator->calculateTravelDuration($e)), 2),
            'total_grand_deplacement_count' => $allEntries->filter(fn ($e) => ($e->chantier->distance_km ?? 0) > 50)->count(),

            'count_entries' => $scopedEntries->count(),
            'period_label' => $month ? $month->translatedFormat('F Y') : 'Total cumulé',
        ];
    }
}
