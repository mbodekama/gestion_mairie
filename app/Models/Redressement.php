<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Dossier de redressement issu d'un contrôle défaillant. Porte les montants
 * redressés et génère des émissions de taxe complémentaires recouvrables.
 */
class Redressement extends Model
{
    protected $table = 'redressement';
    protected $guarded = ['id'];

    protected $casts = [
        'montant_droits'    => 'decimal:2',
        'montant_penalites' => 'decimal:2',
        'montant_total'     => 'decimal:2',
        'date_redressement' => 'date',
    ];

    public function controleFiscal(): BelongsTo
    {
        return $this->belongsTo(ControleFiscal::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    /** Émissions complémentaires générées par ce redressement. */
    public function emissionsTaxe(): HasMany
    {
        return $this->hasMany(EmissionTaxe::class);
    }
}
