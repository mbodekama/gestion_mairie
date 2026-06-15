<?php

namespace App\Models;

use App\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Dossier maître d'un contrôle fiscal, piloté par un workflow à états
 * (etat_controle) et transitions validées (transition_controle).
 * Les pièces jointes (rapport, PV) sont gérées via HasDocuments.
 */
class ControleFiscal extends Model
{
    use HasDocuments;

    protected $table = 'controle_fiscal';
    protected $guarded = ['id'];

    protected $casts = [
        'periode_debut'    => 'date',
        'periode_fin'      => 'date',
        'date_instruction' => 'date',
        'date_validation'  => 'date',
        'date_execution'   => 'date',
        'date_cloture'     => 'date',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function agentInstructeur(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_instructeur_id');
    }

    public function etatControle(): BelongsTo
    {
        return $this->belongsTo(EtatControle::class);
    }

    /** Convocation générée à la validation du contrôle. */
    public function convocation(): BelongsTo
    {
        return $this->belongsTo(Convocation::class);
    }

    public function constats(): HasMany
    {
        return $this->hasMany(ControleConstat::class);
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueControle::class);
    }

    public function redressement(): HasOne
    {
        return $this->hasOne(Redressement::class);
    }

    /** Le contrôle est-il dans un état final (clôturé ou en redressement) ? */
    public function estFinal(): bool
    {
        return (bool) $this->etatControle?->est_final;
    }
}
