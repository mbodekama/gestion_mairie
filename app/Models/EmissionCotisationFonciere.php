<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmissionCotisationFonciere extends Model
{
    protected $table = 'emission_cotisation_fonciere';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected $casts = [
        'ca_annuel'        => 'decimal:2',
        'montant'          => 'decimal:2',
        'montant_periode'  => 'decimal:2',
        'montant_prorata'  => 'decimal:2',
        'date_declaration' => 'date',
        'date_liquidation' => 'date',
        'created_at'       => 'datetime',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function natureTaxe(): BelongsTo
    {
        return $this->belongsTo(NatureTaxe::class);
    }

    public function baremeCotisation(): BelongsTo
    {
        return $this->belongsTo(BaremeCotisationFonciere::class, 'bareme_cotisation_id');
    }

    public function periodicite(): BelongsTo
    {
        return $this->belongsTo(Periodicite::class);
    }

    public function exerciceFiscal(): BelongsTo
    {
        return $this->belongsTo(ExerciceFiscal::class);
    }

    public function reglements(): HasMany
    {
        return $this->hasMany(ReglementTaxe::class, 'emission_cotisation_id');
    }
}
