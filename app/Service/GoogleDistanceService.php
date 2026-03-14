<?php

namespace App\Service;

use Exception;
use Illuminate\Support\Facades\Http;
use Log;

class GoogleDistanceService
{
    protected string $apiKey;

    protected string $origin;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_key');
        $this->origin = config('services.google.depot_address');
    }

    /**
     * Calcule la distance routière entre le dépôt et une adresse de chantier.
     *
     * * @param string $destination L'adresse complète du chantier
     * @return float|null Distance en kilomètres ou null en cas d'erreur
     */
    public function getDistanceInKm(string $destination): ?float
    {
        if (empty($this->apiKey)) {
            Log::error('Google Maps API Key manquante dans la configuration.');

            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                'origins' => $this->origin,
                'destinations' => $destination,
                'mode' => 'driving',
                'key' => $this->apiKey,
                'units' => 'metric',
            ]);

            if ($response->failed()) {
                throw new Exception("Erreur de connexion à l'API Google Maps.");
            }

            $data = $response->json();

            // Vérification du statut global de la réponse
            if (($data['status'] ?? '') !== 'OK') {
                throw new Exception('Erreur API Google : '.($data['error_message'] ?? $data['status']));
            }

            // Extraction de la distance
            $element = $data['rows'][0]['elements'][0] ?? null;

            if (! $element || $element['status'] !== 'OK') {
                Log::warning("Impossible de trouver un itinéraire pour l'adresse : {$destination}");

                return null;
            }

            // Google retourne la distance en mètres
            $distanceInMeters = $element['distance']['value'];

            return round($distanceInMeters / 1000, 2);

        } catch (Exception $e) {
            Log::error('Échec du calcul de distance Google : '.$e->getMessage());

            return null;
        }
    }
}
