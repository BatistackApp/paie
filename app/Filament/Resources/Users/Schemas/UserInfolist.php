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
                Section::make('Informations de bord')
                    ->description(fn () => 'Analyse basée sur un contrat de '.auth()->user()->weekly_contract_hours.'h/semaine')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('work_h')
                                    ->label('Travail Effectif')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record, now())['work_hours'].' h')
                                    ->badge()->color('success'),

                                TextEntry::make('extra_25')
                                    ->label('Heures Supp. 25%')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record, now())['extra_25'].' h')
                                    ->badge()->color('warning'),

                                TextEntry::make('extra_50')
                                    ->label('Heures Supp. 50%')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record, now())['extra_50'].' h')
                                    ->badge()->color('danger'),

                                TextEntry::make('rc_hours')
                                    ->label('Repos Comp. Pris')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record, now())['rc_hours_deducted'].' h')
                                    ->badge()->color('primary')
                                    ->hint('Heures déduites des HS'),

                                TextEntry::make('travel_h')
                                    ->label('Trajets (Taux Normal)')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record, now())['travel_hours'].' h')
                                    ->badge()->color('info'),

                                TextEntry::make('gd_days')
                                    ->label('Grands Déplacements')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record, now())['gd_count'])
                                    ->badge()->color('gray'),
                            ]),
                    ]),

                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Cumuls & Historique')
                            ->collapsed()
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('total_work')
                                            ->label('Travail Total Cumulé')
                                            // Utilisation de l'appel sans paramètre de mois pour le total
                                            ->state(fn (User $record) => $statsService->getStatsForUser($record)['total_work'].' h')
                                            ->badge()->color('gray'),

                                        TextEntry::make('total_extra')
                                            ->label('Total HS (25%) cumulées')
                                            ->state(fn (User $record) => $statsService->getStatsForUser($record)['total_extra_25'].' h')
                                            ->badge()->color('gray'),

                                        TextEntry::make('contract_base')
                                            ->label('Base Contrat')
                                            ->state(fn (User $record) => $record->weekly_contract_hours.' h / semaine')
                                            ->icon('heroicon-m-document-text'),
                                    ]),
                            ]),

                        Section::make('Solde des Congés & Repos')
                            ->description(fn (User $record) => 'Calculé depuis le '.($record->hired_at?->format('d/m/Y') ?? "sa date d'inscription"))
                            ->icon('heroicon-m-calendar-days')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('cp_balance')
                                            ->label('Solde Congés Payés')
                                            ->state(fn (User $record) => $statsService->getStatsForUser($record)['cp_balance'].' j')
                                            ->color('success')
                                            ->weight('bold')
                                            ->size('lg'),

                                        TextEntry::make('cp_taken')
                                            ->label('Congés déjà pris')
                                            ->state(fn (User $record) => $statsService->getStatsForUser($record)['cp_taken'].' j')
                                            ->color('gray'),

                                        TextEntry::make('cp_acquired')
                                            ->label('Total acquis (estimé)')
                                            ->state(fn (User $record) => $statsService->getStatsForUser($record)['cp_acquired'].' j')
                                            ->hint('Base 2.5j / mois'),
                                    ]),
                            ]),
                    ]),

                Section::make('Informations')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Identité')
                                    ->icon(Phosphor::UserCircle)
                                    ->color('primary'),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->color('primary')
                                    ->icon(Phosphor::Envelope)
                                    ->url(fn (User $user) => 'mailto:'.$user->email),

                                TextEntry::make('created_at')
                                    ->label('Inscrit le')
                                    ->date('d/m/Y')
                                    ->icon(Phosphor::Calendar)
                                    ->color('primary'),
                            ]),
                    ]),
            ]);
    }
}
