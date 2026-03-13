<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum CcnZone: string implements HasLabel
{
    case ZONE_1 = 'zone_1'; // 0-10km
    case ZONE_2 = 'zone_2'; // 10-20km
    case ZONE_3 = 'zone_3'; // 20-30km
    case ZONE_4 = 'zone_4'; // 40-50km
    case ZONE_5 = 'zone_5'; // 40-50km (limite petit déplacement)
    case GRAND_DEPLACEMENT = 'grand_deplacement'; // > 50km

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::ZONE_1 => 'Zone 1 (0-10km)',
            self::ZONE_2 => 'Zone 2 (10-20km)',
            self::ZONE_3 => 'Zone 3 (20-30km)',
            self::ZONE_4 => 'Zone 4 (30-40km)',
            self::ZONE_5 => 'Zone 5 (40-50km)',
            self::GRAND_DEPLACEMENT => 'Grand Déplacement (> 50km)',
        };
    }
}
