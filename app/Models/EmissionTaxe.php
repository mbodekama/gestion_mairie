<?php

namespace App\Models;

use App\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmissionTaxe extends Model
{
    use HasDocuments;


    protected $table = 'emission_taxe';
    protected $guarded = ['id'];

    protected $casts = [
        'ca_annuel'        => 'decimal:2',
        'montant_annuel'   => 'decimal:2',
        'montant_periode'  => 'decimal:2',
        'montant_prorata'  => 'decimal:2',
        'date_declaration' => 'date',
        'date_liquidation' => 'date',
        'supprime_le'      => 'datetime',
    ];

    /** Exclut automatiquement les émissions supprimées (soft-delete) de toutes les requêtes. */
    protected static function booted(): void
    {
        static::addGlobalScope('nonSupprime', fn (Builder $query) => $query->whereNull('emission_taxe.supprime_le'));
    }

    public function estSupprime(): bool
    {
        return $this->supprime_le !== null;
    }

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
        // Les règlements annulés ne réduisent pas le solde.
        $totalRegle = $this->reglements()->whereNull('annule_le')->sum('montant_impute');
        // Base = prorata si renseigné (> 0), sinon montant annuel.
        $base = $this->montant_prorata > 0
            ? (string) $this->montant_prorata
            : (string) $this->montant_annuel;

        return bcsub($base, (string) $totalRegle, 2);
    }
}
