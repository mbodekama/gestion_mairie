<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmissionTaxe extends Model
{
    protected $table = 'emission_taxe';
    protected $guarded = ['id'];

    protected $casts = [
        'ca_annuel'        => 'decimal:2',
        'montant_annuel'   => 'decimal:2',
        'montant_periode'  => 'decimal:2',
        'montant_prorata'  => 'decimal:2',
        'date_declaration' => 'date',
        'date_liquidation' => 'date',
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
        return $this->hasMany(ReglementTaxe::class);
    }

    public function soldeDu(): string
    {
        $totalRegle = $this->reglements()->sum('montant_impute');
        return bcsub((string) $this->montant_prorata ?: (string) $this->montant_annuel, (string) $totalRegle, 2);
    }
}
