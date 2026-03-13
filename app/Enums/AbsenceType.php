<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum AbsenceType: string implements HasLabel
{
    case CONGE_PAYE = 'conge_paye';
    case RTT = 'rtt';
    case MALADIE = 'maladie';
    case ACCIDENT_TRAVAIL = 'accident_travail';
    case REPOS_COMPENSATEUR = 'repos_compensateur';
    case SANS_SOLDE = 'sans_solde';
    case INJUSTIFIE = 'injustifie';

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::CONGE_PAYE => 'Congé payé',
            self::RTT => 'RTT',
            self::MALADIE => 'Maladie',
            self::ACCIDENT_TRAVAIL => 'Accident de travail',
            self::REPOS_COMPENSATEUR => 'Repos compensateur',
            self::SANS_SOLDE => 'Sans solde',
            self::INJUSTIFIE => 'Injustifié',
        };
    }
}
