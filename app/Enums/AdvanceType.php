<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AdvanceType: string implements HasLabel, HasColor
{
    case GrandDeplacement = 'grand_deplacement';
    case Salarie = 'salarie';


    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::GrandDeplacement => 'Grand Deplacement',
            self::Salarie => 'Salaire',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::GrandDeplacement => 'warning',
            self::Salarie => 'primary',
        };
    }
}
