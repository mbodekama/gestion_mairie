<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    protected $table = 'commune';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function sousPrefecture(): BelongsTo
    {
        return $this->belongsTo(SousPrefecture::class);
    }

    public function quartiers(): HasMany
    {
        return $this->hasMany(Quartier::class);
    }

    public function zonesFiscales(): HasMany
    {
        return $this->hasMany(ZoneFiscale::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }

    public function collectivites(): HasMany
    {
        return $this->hasMany(Collectivite::class);
    }
}
