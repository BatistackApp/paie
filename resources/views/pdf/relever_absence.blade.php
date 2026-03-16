<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white p-10 text-gray-900">
<h1 class="text-2xl font-extrabold text-red-800 mb-6 border-l-8 border-red-800 pl-4">RÉCAPITULATIF DES ABSENCES</h1>

<div class="space-y-4">
    @foreach($absences as $absence)
        <div class="border rounded-lg p-4 flex justify-between items-center">
            <div>
                <p class="text-lg font-bold">{{ $absence->user->name }}</p>
                <p class="text-sm text-gray-500 uppercase tracking-widest">{{ $absence->absence_type->value }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold">Du {{ $absence->start_date->format('d/m/Y') }}</p>
                <p class="text-sm font-semibold text-red-600">Au {{ $absence->end_date->format('d/m/Y') }}</p>
            </div>
        </div>
    @endforeach
</div>
</body>
</html>
