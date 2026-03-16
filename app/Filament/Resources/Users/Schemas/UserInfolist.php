<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use App\Service\UserStatsService;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $statsService = app(UserStatsService::class);

        return $schema
            ->components([
                Section::make('Informations Salarié')
                    ->columnSpanFull()
                    ->icon(Phosphor::IdentificationCard)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Salarié')
                                    ->icon(Phosphor::UserCircle),

                                TextEntry::make('email')
                                    ->label('Contact')
                                    ->icon(Phosphor::Envelope),

                                TextEntry::make('hired_at')
                                    ->label('Date d\'embauche')
                                    ->date('d/m/Y')
                                    ->icon(Phosphor::Calendar)
                                    ->placeholder('Non renseignée'),
                            ]),
                    ]),
                // Utilisation d'un cache local pour cette méthode de configuration
                Section::make('Indicateurs Annuels & RH')
                    ->description(fn(User $record) => "Suivi des compteurs globaux au " . now()->format('d/m/Y'))
                    ->icon(Phosphor::AddressBook)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('cp_balance')
                                    ->label('Solde Congés Payés')
                                    ->state(fn(User $record) => app(UserStatsService::class)->getStatsForUser($record)['cp_balance'] . ' j')
                                    ->color('success')
                                    ->weight('bold'),

                                TextEntry::make('annual_ot')
                                    ->label('Contingent HS (Annuel)')
                                    ->state(fn(User $record) => app(UserStatsService::class)->getStatsForUser($record)['annual_overtime'] . ' / 220 h')
                                    ->color(fn($state, User $record) => app(UserStatsService::class)->getStatsForUser($record)['contingent_percent'] > 85 ? 'danger' : 'primary')
                                    ->weight('bold'),

                                TextEntry::make('total_gd')
                                    ->label('Total Grands Dépl. (Année)')
                                    ->state(fn(User $record) => app(UserStatsService::class)->getStatsForUser($record)['total_gd_count'] . ' j')
                                    ->icon(Phosphor::Truck)
                                    ->color('gray'),
                            ]),
                    ]),

                Section::make('Analyse du Mois en Cours')
                    ->description(fn() => "Détail de l'activité pour " . now()->translatedFormat('F Y'))
                    ->icon(Phosphor::Calendar)
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('month_work')
                                    ->label('Travail Effectif')
                                    ->state(fn(User $record) => app(UserStatsService::class)->getStatsForUser($record, now())['month_work'] . ' h')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('month_hs')
                                    ->label('HS (Total Majoré)')
                                    ->state(fn(User $record) => (app(UserStatsService::class)->getStatsForUser($record, now())['month_extra_25'] + app(UserStatsService::class)->getStatsForUser($record, now())['month_extra_50']) . ' h')
                                    ->badge()
                                    ->color('warning'),

                                TextEntry::make('month_travel')
                                    ->label('Heures Trajet')
                                    ->state(fn(User $record) => app(UserStatsService::class)->getStatsForUser($record, now())['month_travel'] . ' h')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('month_gd')
                                    ->label('Grands Dépl.')
                                    ->state(fn(User $record) => app(UserStatsService::class)->getStatsForUser($record, now())['month_gd_count'] . ' j')
                                    ->badge()
                                    ->color('danger')
                                    ->hint('Distance > 50km'),

                                TextEntry::make('gd_ratio')
                                    ->label('% Activité GD')
                                    ->state(fn(User $record) => round((app(UserStatsService::class)->getStatsForUser($record, now())['month_gd_count'] / max(1, app(UserStatsService::class)->getStatsForUser($record, now())['month_work'] / 7)) * 100) . ' %')
                                    ->color('gray'),
                            ]),
                    ]),
            ]);
    }
}
