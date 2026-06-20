<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Objectif extends Model
{
    protected $table = 'objectif';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'montant'        => 'decimal:2',
        'montant_revise' => 'decimal:2',
        'periode_debut'  => 'date',
        'periode_fin'    => 'date',
        'created_at'     => 'datetime',
    ];

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function exerciceFiscal(): BelongsTo
    {
        return $this->belongsTo(ExerciceFiscal::class);
    }
}
