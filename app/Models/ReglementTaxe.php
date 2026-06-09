<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReglementTaxe extends Model
{
    protected $table = 'reglement_taxe';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'date_reglement' => 'date',
        'montant'        => 'decimal:2',
        'montant_impute' => 'decimal:2',
        'created_at'     => 'datetime',
    ];

    public function emissionTaxe(): BelongsTo
    {
        return $this->belongsTo(EmissionTaxe::class);
    }

    public function emissionCotisation(): BelongsTo
    {
        return $this->belongsTo(EmissionCotisationFonciere::class, 'emission_cotisation_id');
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function recette(): BelongsTo
    {
        return $this->belongsTo(Recette::class);
    }

    public function exerciceFiscal(): BelongsTo
    {
        return $this->belongsTo(ExerciceFiscal::class);
    }

    public function modeReglement(): BelongsTo
    {
        return $this->belongsTo(ModeReglement::class);
    }

    public function typeReglement(): BelongsTo
    {
        return $this->belongsTo(TypeReglement::class);
    }

    public function banque(): BelongsTo
    {
        return $this->belongsTo(Banque::class);
    }
}
