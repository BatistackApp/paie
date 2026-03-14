<?php

namespace App\Models;

use App\Enums\AbsenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'absence_type',
        'start_date',
        'end_date',
        'comment',
        'is_validated',
        'validated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'absence_type' => AbsenceType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_validated' => 'boolean',
            'validated_at' => 'datetime',
        ];
    }
}
