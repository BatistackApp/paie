<?php

namespace App\Filament\Resources\Absences\Schemas;

use App\Models\Absence;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class AbsenceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Absence')
                            ->columns(1)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Salarié')
                                    ->weight(FontWeight::Bold),

                                TextEntry::make('absence_type')
                                    ->label('Type d\'absence')
                                    ->badge(),

                                TextEntry::make('start_date')
                                    ->label('Date de début')
                                    ->date('d/m/Y'),

                                TextEntry::make('end_date')
                                    ->label('Date de fin')
                                    ->date('d/m/Y'),

                                TextEntry::make('is_validated')
                                    ->label('')
                                    ->color(fn(Absence $record) => $record->is_validated ? 'success' : 'danger')
                                    ->icon(fn(Absence $record) => $record->is_validated ? Phosphor::CheckCircle : Phosphor::XCircle)
                                    ->formatStateUsing(fn(Absence $record) => $record->is_validated ? "Validé le {$record->validated_at->format('d/m/Y')}" : 'Non Valider'),
                            ]),

                        Section::make('Recommandation')
                            ->columns(1)
                            ->icon(Phosphor::Info)
                            ->iconColor('info')
                            ->schema([
                                TextEntry::make('procedure')
                                    ->label('Procédure à suivre')
                                    ->state(fn(Absence $record) => match ($record->absence_type->value) {
                                        'intemperie' => 'Déclarer sur le portail CIBTP. Indemnisation à partir de la 2ème heure.',
                                        'maladie' => 'Récupérer l\'arrêt de travail. Vérifier le maintien de salaire après carence.',
                                        'accident_travail' => 'S\'assurer que la DAT a été envoyée à la CPAM sous 48h.',
                                        'injustifie' => 'Absence non documentée. Envoyer un courrier de demande de justification.',
                                        'conge_paye', 'rtt' => 'Validation conforme au planning prévisionnel.',
                                        'sans_solde' => 'Vérifier l\'impact sur le calcul de la prime d\'ancienneté.',
                                        default => 'Suivi standard RH.',
                                    })
                                    ->color('warning')
                                    ->weight('bold')
                                    ->bulleted(),
                            ]),
                    ]),
            ]);
    }
}
