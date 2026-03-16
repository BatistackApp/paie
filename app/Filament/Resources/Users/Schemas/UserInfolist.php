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
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('month_work')
                                    ->label('Travail (Mois en cours): '.now()->monthName)
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record)['month_work_hours'].' h')
                                    ->badge()->color('success'),

                                TextEntry::make('month_travel')
                                    ->label('Trajet (Mois en cours): '.now()->monthName)
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record)['month_travel_hours'].' h')
                                    ->badge()->color('info'),

                                TextEntry::make('month_grand_deplacement')
                                    ->label('Grand deplacement (Mois): '.now()->monthName)
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record)['month_grand_deplacement_count'])
                                    ->badge()->color('danger'),

                                // --- LIGNE 2 : CUMULS ---
                                TextEntry::make('total_work')
                                    ->label('Travail (Cumul total)')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record)['total_work_hours'].' h')
                                    ->badge()->color('gray'),

                                TextEntry::make('total_travel')
                                    ->label('Trajets (Cumul total)')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record)['total_travel_hours'].' h')
                                    ->badge()->color('gray'),

                                TextEntry::make('total_grand_deplacement')
                                    ->label('Grand deplacement (Cumul total)')
                                    ->state(fn (User $record) => $statsService->getStatsForUser($record)['total_grand_deplacement_count'])
                                    ->badge()->color('gray'),
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
