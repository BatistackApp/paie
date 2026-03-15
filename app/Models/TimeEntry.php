<?php

namespace App\Models;

use App\Observers\TimeEntryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([TimeEntryObserver::class])]
class TimeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chantier_id',
        'entry_date',
        'depart_depot',
        'embauche_chantier',
        'debauche_chantier',
        'retour_depot',
        'break_duration_minute',
        'has_meal',
        'has_night',
        'is_validated',
        'validated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chantier(): BelongsTo
    {
        return $this->belongsTo(Chantier::class);
    }

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'depart_depot' => 'datetime',
            'embauche_chantier' => 'datetime',
            'debauche_chantier' => 'datetime',
            'retour_depot' => 'datetime',
            'is_validated' => 'boolean',
            'has_meal' => 'boolean',
            'has_night' => 'boolean',
            'validated_at' => 'datetime',
        ];
    }

    public function validate(): void
    {
        $this->update([
            'is_validated' => true,
            'validated_at' => now(),
        ]);
    }

    public function unvalidate(): void
    {
        $this->update([
            'is_validated' => false,
            'validated_at' => null,
        ]);
    }
}
