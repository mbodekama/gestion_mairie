<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collectivite extends Model
{
    protected $table = 'collectivite';
    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function typeCollectivite(): BelongsTo
    {
        return $this->belongsTo(TypeCollectivite::class);
    }

    public function recette(): BelongsTo
    {
        return $this->belongsTo(Recette::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function contribuables(): HasMany
    {
        return $this->hasMany(Contribuable::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }

    public function exercicesFiscaux(): HasMany
    {
        return $this->hasMany(ExerciceFiscal::class);
    }

    public function objectifs(): HasMany
    {
        return $this->hasMany(Objectif::class);
    }
}
