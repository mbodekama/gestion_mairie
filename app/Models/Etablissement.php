<?php

namespace App\Models;

use App\Traits\HasDocuments;
use App\Traits\HasHistorique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
    use HasDocuments, HasHistorique;

    protected $table = 'etablissement';
    protected $guarded = ['id'];

    protected array $auditExclu  = ['collectivite_id', 'supprime_le', 'created_by', 'updated_by'];
    protected array $auditLabels = [
        'denomination'        => 'Dénomination',
        'type_etablissement'  => 'Type',
        'activite_id'         => 'Activité ID',
        'commune_id'          => 'Commune ID',
        'zone_fiscale_id'     => 'Zone fiscale ID',
        'adresse'             => 'Adresse',
        'date_debut_activite' => 'Début d\'activité',
        'date_cessation'      => 'Date de cessation',
        'telephone'           => 'Téléphone',
        'email'               => 'Email',
        'ca_reference'        => 'CA de référence',
        'statut'              => 'Statut',
    ];

    protected $casts = [
        'surface'              => 'decimal:2',
        'ca_reference'         => 'decimal:2',
        'date_debut_activite'  => 'date',
        'date_cessation'       => 'date',
        'date_transfert'       => 'date',
        'date_sommeil'         => 'date',
        'supprime_le'          => 'datetime',
    ];

    public function contribuable(): BelongsTo
    {
        return $this->belongsTo(Contribuable::class);
    }

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    public function voie(): BelongsTo
    {
        return $this->belongsTo(Voie::class);
    }

    public function zoneFiscale(): BelongsTo
    {
        return $this->belongsTo(ZoneFiscale::class);
    }

    public function emissionsTaxe(): HasMany
    {
        return $this->hasMany(EmissionTaxe::class);
    }

    public function emissionsCotisationFonciere(): HasMany
    {
        return $this->hasMany(EmissionCotisationFonciere::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    public function convocations(): HasMany
    {
        return $this->hasMany(Convocation::class);
    }
}
