<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white p-8 text-gray-800">
<div class="flex justify-between items-center border-b-2 border-blue-600 pb-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-blue-900">RELEVÉ D'HEURES</h1>
        <p class="text-sm text-gray-500 italic">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="text-right">
        <p class="font-bold text-lg">C2ME</p>
        <p class="text-sm">Service Planning / Paye</p>
    </div>
</div>

<table class="w-full text-left border-collapse">
    <thead>
    <tr class="bg-blue-50 text-blue-900 uppercase text-xs">
        <th class="p-3 border">Date</th>
        <th class="p-3 border">Salarié</th>
        <th class="p-3 border">Chantier</th>
        <th class="p-3 border text-center">Travail (h)</th>
        <th class="p-3 border text-center">Trajet (h)</th>
    </tr>
    </thead>
    <tbody>
    @php
        $calc = app(App\Service\CcnCalculatorService::class);
        $totalW = 0; $totalT = 0;
    @endphp
    @foreach($entries as $entry)
        @php
            $w = $calc->calculateWorkDuration($entry);
            $t = $calc->calculateTravelDuration($entry);
            $totalW += $w; $totalT += $t;
        @endphp
        <tr class="text-sm hover:bg-gray-50">
            <td class="p-3 border">{{ $entry->entry_date->format('d/m/Y') }}</td>
            <td class="p-3 border font-medium">{{ $entry->user->name }}</td>
            <td class="p-3 border">{{ $entry->chantier->name }}</td>
            <td class="p-3 border text-center font-bold">{{ $w }}</td>
            <td class="p-3 border text-center text-blue-600">{{ $t }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr class="bg-gray-100 font-bold">
        <td colspan="3" class="p-3 border text-right uppercase">Totaux</td>
        <td class="p-3 border text-center text-green-700 text-lg">{{ $totalW }} h</td>
        <td class="p-3 border text-center text-blue-700 text-lg">{{ $totalT }} h</td>
    </tr>
    </tfoot>
</table>

<div class="mt-12 grid grid-cols-2 gap-8 text-center text-xs text-gray-400">
    <div class="border-t pt-2">Signature Salarié</div>
    <div class="border-t pt-2">Signature Direction</div>
</div>
</body>
</html>
