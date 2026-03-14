<?php

namespace App\Service;

use App\Models\TimeEntry;
use Illuminate\Support\Carbon;

class CcnCalculatorService
{
    /**
     * Calcule la durée du travail effectif en heures décimales.
     * Formule : (Débauche - Embauche) - Durée de la pause.
     */
    public function calculateWorkDuration(TimeEntry $entry): float
    {
        if (! $entry->embauche_chantier || ! $entry->debauche_chantier) {
            return 0.0;
        }

        $start = Carbon::parse($entry->embauche_chantier);
        $end = Carbon::parse($entry->debauche_chantier);

        // Différence totale sur site en minutes
        $totalMinutesOnSite = $start->diffInMinutes($end);

        // On soustrait la pause (60 min par défaut)
        $workMinutes = $totalMinutesOnSite - ($entry->break_duration_minute ?? 60);

        return round(max(0, $workMinutes / 60), 2);
    }

    /**
     * Calcule la durée totale des trajets payés en heures décimales.
     * Formule : (Dépôt -> Chantier) + (Chantier -> Dépôt).
     */
    public function calculateTravelDuration(TimeEntry $entry): float
    {
        $minutesTrajet = 0;

        // Trajet Aller : Temps écoulé entre le départ du dépôt et l'embauche effective
        if ($entry->depart_depot && $entry->embauche_chantier) {
            $minutesTrajet += Carbon::parse($entry->depart_depot)->diffInMinutes(Carbon::parse($entry->embauche_chantier));
        }

        // Trajet Retour : Temps écoulé entre la débauche du chantier et le retour au dépôt
        if ($entry->debauche_chantier && $entry->retour_depot) {
            $minutesTrajet += Carbon::parse($entry->debauche_chantier)->diffInMinutes(Carbon::parse($entry->retour_depot));
        }

        return round($minutesTrajet / 60, 2);
    }

    /**
     * Détermine la Zone CCN en fonction de la distance du chantier.
     * * Zone 1 : 0-10km
     * Zone 2 : 10-20km
     * Zone 3 : 20-30km
     * Zone 4 : 30-40km
     * Zone 5 : 40-50km
     * > 50km : Grand Déplacement
     */
    public function determineCcnZone(float $distance): string
    {
        if ($distance > 50) {
            return 'Grand Déplacement';
        }

        if ($distance <= 0) {
            return 'Atelier / Dépôt';
        }

        $zone = (int) ceil($distance / 10);

        return 'Zone '.min($zone, 5);
    }
}
