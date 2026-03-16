<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: 'Inter', sans-serif; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body class="bg-white p-12 text-gray-800">

<!-- HEADER -->
<div class="flex justify-between items-start border-b-4 border-blue-700 pb-6 mb-8">
    <div>
        <h1 class="text-4xl font-extrabold text-blue-900 tracking-tight">SYNTHÈSE DE PAYE</h1>
        <p class="text-sm text-gray-500 mt-1 italic">Période du : <span class="font-bold text-gray-700">{{ $startDate }}</span> au <span class="font-bold text-gray-700">{{ $endDate }}</span></p>
    </div>
    <div class="text-right">
        <h2 class="text-xl font-bold text-gray-900">C2ME</h2>
        <p class="text-xs text-gray-500">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</div>

<!-- INFOS SALARIÉ -->
<div class="grid grid-cols-2 gap-8 mb-10 bg-gray-50 p-6 rounded-xl border border-gray-200">
    <div>
        <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-2">Salarié</h3>
        <p class="text-xl font-bold text-gray-900">{{ $user->name }}</p>
        <p class="text-sm text-gray-600">{{ $user->email }}</p>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-1">Contrat</h3>
            <p class="text-sm font-semibold">{{ $user->weekly_contract_hours }}h / semaine</p>
        </div>
        <div>
            <h3 class="text-xs font-bold text-blue-700 uppercase tracking-widest mb-1">Embauche</h3>
            <p class="text-sm font-semibold">{{ $user->hired_at?->format('d/m/Y') ?? 'N/C' }}</p>
        </div>
    </div>
</div>

<!-- BARRE DE STATS RAPIDES -->
<div class="grid grid-cols-4 gap-4 mb-10">
    <div class="bg-blue-600 text-white p-4 rounded-lg shadow-sm">
        <p class="text-xs uppercase font-bold opacity-80">Travail Effectif</p>
        <p class="text-2xl font-black">{{ number_format($entries->sum('work_duration'), 2) }} h</p>
    </div>
    <div class="bg-indigo-600 text-white p-4 rounded-lg shadow-sm">
        <p class="text-xs uppercase font-bold opacity-80">Heures Trajet</p>
        <p class="text-2xl font-black">{{ number_format($entries->sum('travel_duration'), 2) }} h</p>
    </div>
    <div class="bg-red-600 text-white p-4 rounded-lg shadow-sm">
        <p class="text-xs uppercase font-bold opacity-80">Grands Dépl.</p>
        <p class="text-2xl font-black">{{ $entries->filter(fn($e) => ($e->chantier->distance_km ?? 0) > 50)->count() }} j</p>
    </div>
    <div class="bg-emerald-600 text-white p-4 rounded-lg shadow-sm">
        <p class="text-xs uppercase font-bold opacity-80">Paniers Repas</p>
        <p class="text-2xl font-black">{{ $entries->where('has_meal', true)->count() }}</p>
    </div>
</div>

<!-- PLANNING DÉTAILLÉ -->
<div class="mb-10">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <span class="w-2 h-6 bg-blue-700 rounded-full"></span>
        Détail des entrées de planning
    </h3>
    <table class="w-full text-left border-collapse">
        <thead>
        <tr class="bg-gray-100 text-xs uppercase text-gray-600">
            <th class="p-3 border">Date</th>
            <th class="p-3 border">Chantier / Mission</th>
            <th class="p-3 border text-center">Travail</th>
            <th class="p-3 border text-center">Trajet</th>
            <th class="p-3 border text-center">GD</th>
            <th class="p-3 border text-center">Panier</th>
        </tr>
        </thead>
        <tbody class="text-sm">
        @foreach($entries->sortBy('entry_date') as $entry)
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 border font-medium">{{ $entry->entry_date->translatedFormat('D d/m') }}</td>
                <td class="p-3 border">
                    <span class="font-bold">{{ $entry->chantier->nom }}</span>
                    <p class="text-xs text-gray-400">{{ $entry->chantier->distance_km }} km du dépôt</p>
                </td>
                <td class="p-3 border text-center font-bold">{{ number_format($entry->work_duration, 2) }}h</td>
                <td class="p-3 border text-center text-blue-600">{{ number_format($entry->travel_duration, 2) }}h</td>
                <td class="p-3 border text-center">
                    @if(($entry->chantier->distance_km ?? 0) > 50) <span class="text-red-600 font-bold">Oui</span> @else - @endif
                </td>
                <td class="p-3 border text-center">
                    @if($entry->has_meal) ✅ @else - @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- ABSENCES ET ACOMPTES -->
<div class="grid grid-cols-2 gap-12">
    <!-- TABLE ABSENCES -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-2 h-6 bg-orange-500 rounded-full"></span>
            Absences & Congés
        </h3>
        @if($absences->isEmpty())
            <p class="text-sm text-gray-400 italic">Aucune absence signalée sur la période.</p>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                <tr class="border-b-2 border-gray-100 text-gray-500 uppercase text-xs">
                    <th class="py-2">Type</th>
                    <th class="py-2">Période</th>
                    <th class="py-2 text-right">Jours</th>
                </tr>
                </thead>
                <tbody>
                @foreach($absences as $absence)
                    <tr class="border-b border-gray-50">
                        <td class="py-2 font-medium">{{ $absence->absence_type->getLabel() ?? $absence->absence_type->value }}</td>
                        <td class="py-2 text-gray-500">{{ $absence->start_date->format('d/m') }} au {{ $absence->end_date->format('d/m') }}</td>
                        <td class="py-2 text-right font-bold">{{ app(App\Service\AbsenceService::class)->calculateAbsenceDays($absence) }} j</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- TABLE ACOMPTES -->
    <div>
        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <span class="w-2 h-6 bg-emerald-500 rounded-full"></span>
            Acomptes versés
        </h3>
        @if($advances->isEmpty())
            <p class="text-sm text-gray-400 italic">Aucun acompte versé sur la période.</p>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                <tr class="border-b-2 border-gray-100 text-gray-500 uppercase text-xs">
                    <th class="py-2">Date</th>
                    <th class="py-2">Type</th>
                    <th class="py-2 text-right">Montant</th>
                </tr>
                </thead>
                <tbody>
                @foreach($advances as $advance)
                    <tr class="border-b border-gray-50">
                        <td class="py-2">{{ $advance->date->format('d/m/Y') }}</td>
                        <td class="py-2 italic text-gray-500">{{ $advance->type === 'grand_deplacement' ? 'Grand Dépl.' : 'Salaire' }}</td>
                        <td class="py-2 text-right font-bold text-emerald-700">{{ number_format($advance->amount, 2) }} €</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="font-bold text-gray-900">
                    <td colspan="2" class="py-3 text-right">Total Acomptes</td>
                    <td class="py-3 text-right text-emerald-800 border-t-2 border-emerald-100">{{ number_format($advances->sum('amount'), 2) }} €</td>
                </tr>
                </tfoot>
            </table>
        @endif
    </div>
</div>

<!-- SIGNATURES -->
<div class="mt-20 grid grid-cols-2 gap-20">
    <div class="border-t-2 border-gray-200 pt-4">
        <p class="text-xs font-bold text-gray-400 uppercase mb-12">Signature du Salarié</p>
        <p class="text-[10px] text-gray-300 italic">Précédée de la mention "Lu et approuvé"</p>
    </div>
    <div class="border-t-2 border-gray-200 pt-4 text-right">
        <p class="text-xs font-bold text-gray-400 uppercase mb-12">Cachet et Signature Direction</p>
        <p class="text-xs text-gray-600 font-bold">SARL C2ME</p>
    </div>
</div>

</body>
</html>
