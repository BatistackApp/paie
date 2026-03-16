@php
    $type = $get('absence_type');

    // On extrait la valeur de l'Enum si c'est un objet, sinon on garde la valeur brute
    $typeKey = $type instanceof \UnitEnum ? $type->value : $type;

    $recommendations = [
        'conge_paye' => 'Vérifier le solde de droits acquis et le planning de l\'équipe.',
        'maladie' => '⚠️ Demander l\'arrêt de travail original (Volet 3). Signalement DSN sous 48h.',
        'accident_travail' => '🚨 Établir la déclaration DAT sous 48h. Remettre la feuille de soins au salarié.',
        'intemperie' => '🏗️ Déclaration CIBTP obligatoire sous 48h. Vérifier l\'éligibilité du chantier.',
        'repos_compensateur' => '🕒 Décompte à effectuer sur le compteur d\'heures supplémentaires.',
        'sans_solde' => '📉 Impact sur l\'ancienneté et les droits à congés. Accord écrit recommandé.',
        'injustifie' => '❌ Absence sans motif. Mise en demeure requise après 48h de silence.',
    ];

    $message = $recommendations[$typeKey] ?? 'Vérifier la justification officielle de l\'absence.';
@endphp

<div class="p-4 rounded-lg bg-primary-50 border border-primary-200 text-sm text-primary-700">
    <div class="flex items-center gap-2 font-bold mb-1">
        <b>Procédure conseillée</b>
    </div>
    {{ $message }}
</div>
