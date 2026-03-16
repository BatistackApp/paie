<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\AbsenceType;
use App\Enums\AdvanceType;
use App\Filament\Resources\Users\UserResource;
use App\Models\Chantier;
use App\Models\User;
use App\Service\PdfService;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Illuminate\Support\Carbon;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Fiche d\'un salarié';

    protected static ?string $breadcrumb = 'Fiche d\'un salarié';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Phosphor::Pencil)
                ->label('Modifier le salarié'),

            DeleteAction::make()
                ->icon(Phosphor::Trash)
                ->label('Supprimer le salarié')
                ->requiresConfirmation()
                ->modalHeading('Supprimer le salarié')
                ->modalIcon(Phosphor::Trash),

            Action::make('print')
                ->label('Imprimer le relever')
                ->icon(Phosphor::Printer)
                ->color('gray')
                ->schema([
                    DatePicker::make('until')
                        ->label('Du')
                        ->required(),

                    DatePicker::make('from')
                        ->label('Au')
                        ->required(),
                ])
                ->action(function (User $record, array $data) {

                    $from = Carbon::createFromTimestamp(strtotime($data['until']));
                    $to = Carbon::createFromTimestamp(strtotime($data['from']));

                    $times_entries = $record->timeEntries()
                        ->whereBetween('entry_date', [$from, $to])
                        ->get();
                    $absences = $record->absences()
                        ->whereBetween('start_date', [$from, $to])
                        ->whereBetween('end_date', [$from, $to])
                        ->get();

                    $advances = $record->advances()
                        ->whereBetween('date', [$from, $to])
                        ->get();

                    $pdfContent = app(PdfService::class)->generateFromView('pdf.relever_salarie', [
                        'entries' => $times_entries,
                        'absences' => $absences,
                        'advances' => $advances,
                        'title' => 'Relever d\'heure du salarié',
                        'user' => $record,
                        'startDate' => $data['until'],
                        'endDate' => $data['from'],
                    ]);

                    return response()->streamDownload(
                        fn () => print ($pdfContent),
                        'releve_heures_'.now()->format('Y-m-d').'.pdf'
                    );
                }),

            ActionGroup::make([
                Action::make('create_absence')
                    ->icon(Phosphor::UserMinus)
                    ->label('Ajouter une absence')
                    ->schema([
                        Section::make('Détail de l\'absence')
                            ->columnSpanFull()
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        Select::make('user_id')
                                            ->options(fn () => User::where('is_salarie', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (User $record) => $record->id)
                                            ->required(),

                                        Select::make('absence_type')
                                            ->label('Type')
                                            ->options(AbsenceType::class)
                                            ->required(),
                                    ]),

                                Grid::make()
                                    ->schema([
                                        DatePicker::make('start_date')
                                            ->label('Date de début')
                                            ->required(),

                                        DatePicker::make('end_date')
                                            ->label('Date de fin')
                                            ->required(),
                                    ]),

                                Textarea::make('comment')
                                    ->label('Commentaire')
                                    ->columnSpanFull(),

                                Toggle::make('is_validated')
                                    ->label('Justificatif reçu / Absence validée'),
                            ]),
                    ])
                    ->action(fn (User $record, array $data) => $record->absences()->create($data)),

                Action::make('create_timeentries')
                    ->icon(Phosphor::CalendarPlus)
                    ->label('Ajouter une entrée d\'heure')
                    ->modalWidth(Width::FitContent)
                    ->modalSubmitActionLabel("Ajouter l'entrée")
                    ->schema([
                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                Section::make('Affectation')
                                    ->columnSpan(1)
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Salarié')
                                            ->options(fn () => User::where('is_salarie', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (User $record) => $record->id)
                                            ->required(),

                                        Select::make('chantier_id')
                                            ->options(Chantier::pluck('name', 'id'))
                                            ->label('Chantier')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        DatePicker::make('entry_date')
                                            ->label('Date')
                                            ->required()
                                            ->default(now()),
                                    ]),

                                Section::make('Horaire de la journée')
                                    ->columnSpan(1)
                                    ->columns(2)
                                    ->schema([
                                        TimePicker::make('depart_depot')
                                            ->label('Départ Dépot'),

                                        TimePicker::make('embauche_chantier')
                                            ->label('Embauche sur site')
                                            ->required(),

                                        TimePicker::make('debauche_chantier')
                                            ->label('Débauche sur site')
                                            ->required(),

                                        TimePicker::make('retour_depot')
                                            ->label('Retour Dépot'),

                                        TextInput::make('break_duration_minute')
                                            ->label('Pause (min)')
                                            ->numeric()
                                            ->default(60)
                                            ->suffix('min'),
                                    ]),

                                Section::make('Indemnités et Validations')
                                    ->columnSpan(1)
                                    ->schema([
                                        Toggle::make('has_meal')
                                            ->label('Panier Repas')
                                            ->default(true),

                                        Toggle::make('has_night')
                                            ->label('Nuitée / Grand Déplacement'),

                                        Toggle::make('is_validated')
                                            ->label('Ligne validée pour la paye'),
                                    ]),
                            ]),
                    ])
                    ->action(function (User $record, array $data): void {
                        try {
                            $record->timeEntries()->create($data);
                        } catch (\Exception $exception) {
                            Notification::make()
                                ->danger()
                                ->title('Une erreur est survenue')
                                ->body($exception->getMessage())
                                ->send();
                        }
                    }),

                Action::make('create_advance')
                    ->icon(Phosphor::Bank)
                    ->label('Ajouter un acompte')
                    ->modalWidth(Width::FitContent)
                    ->modalSubmitActionLabel('Ajouter un acompte')
                    ->schema([
                        Section::make('Détail de l\'acompte')
                            ->columnSpanFull()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('Salarié')
                                            ->options(fn () => User::where('is_salarie', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (User $record) => $record->id)
                                            ->required(),

                                        TextInput::make('amount')
                                            ->label('Montant')
                                            ->numeric()
                                            ->prefix('€')
                                            ->required(),

                                        TextInput::make('date')
                                            ->label('Date')
                                            ->default(now())
                                            ->required(),

                                        Select::make('type')
                                            ->label('Type d\'acompte')
                                            ->options(AdvanceType::class)
                                            ->required(),
                                    ]),

                                Textarea::make('reason')
                                    ->label('Motif / Commentaire')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->action(function (User $record, array $data): void {
                        try {
                            $record->advances()->create($data);
                        } catch (\Exception $exception) {
                            Notification::make()
                                ->danger()
                                ->title('Une erreur est survenue')
                                ->body($exception->getMessage())
                                ->send();
                        }
                    }),

            ]),
        ];
    }
}
