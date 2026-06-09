<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
    protected $table = 'etablissement';
    protected $guarded = ['id'];

    protected $casts = [
        'surface'              => 'decimal:2',
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
