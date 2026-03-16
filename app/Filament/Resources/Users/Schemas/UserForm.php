<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations Salarié')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom/Prénom')
                            ->required(),

                        TextInput::make('email')
                            ->label('Email Professionnel')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required(),

                        TextInput::make('password')
                            ->label('Mot de passe')
                            ->password()
                            ->revealable()
                            ->default(Str::random(10))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context) => $context === 'create'),

                        TextInput::make('weekly_contract_hours')
                            ->label('Heures contractuelles / semaine')
                            ->numeric()
                            ->default(35.00)
                            ->suffix('h')
                            ->helperText('Utilisé pour le calcul automatique des heures supplémentaires (25% et 50%).'),

                        DatePicker::make('hired_at')
                            ->label('Date de début de contrat')
                            ->date('d/m/Y'),

                        TextInput::make('cp_carry_over')
                            ->label('Ancien solde de congés')
                            ->numeric()
                            ->default(0)
                            ->suffix('Jours'),

                    ])->columns(2),
            ]);
    }
}
