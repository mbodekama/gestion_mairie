<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    protected $table = 'dossier';
    protected $guarded = ['id'];

    protected $casts = [
        'date_creation' => 'date',
        'date_retour'   => 'date',
        'date_sortie'   => 'date',
        'archive'       => 'boolean',
    ];

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function agentRetrait(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_retrait_id');
    }

    public function serviceOrigine(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_origine_id');
    }

    public function serviceDestination(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_destination_id');
    }

    public function familleEtatDossier(): BelongsTo
    {
        return $this->belongsTo(FamilleEtatDossier::class);
    }

    public function categorieEtatDossier(): BelongsTo
    {
        return $this->belongsTo(CategorieEtatDossier::class);
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueDossier::class);
    }

    public function emissionsTaxe(): HasMany
    {
        return $this->hasMany(EmissionTaxe::class);
    }

    public function emissionsCotisationFonciere(): HasMany
    {
        return $this->hasMany(EmissionCotisationFonciere::class);
    }
}
