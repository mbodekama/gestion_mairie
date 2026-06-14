<?php

namespace App\Models;

use App\Traits\HasDocuments;
use App\Traits\HasHistorique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReglementTaxe extends Model
{
    use HasDocuments, HasHistorique;

    protected $table = 'reglement_taxe';
    public $timestamps = false;
    const UPDATED_AT = null;
    protected $guarded = ['id'];

    protected array $auditExclu  = ['collectivite_id', 'created_by', 'annule_par'];
    protected array $auditLabels = [
        'date_reglement'        => 'Date de règlement',
        'montant'               => 'Montant',
        'montant_impute'        => 'Montant imputé',
        'mode_reglement_id'     => 'Mode de règlement',
        'type_reglement_id'     => 'Type de règlement',
        'numero_quittance'      => 'N° Quittance',
        'annule_le'             => 'Annulé le',
        'motif_annulation'      => 'Motif d\'annulation',
    ];

    protected $casts = [
        'date_reglement' => 'date',
        'montant'        => 'decimal:2',
        'montant_impute' => 'decimal:2',
        'created_at'     => 'datetime',
        'annule_le'      => 'datetime',
    ];

    /** Vrai si le règlement a été annulé. */
    public function estAnnule(): bool
    {
        return $this->annule_le !== null;
    }

    public function annulePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'annule_par');
    }

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
