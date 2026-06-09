<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contribuable extends Model
{
    protected $table = 'contribuable';
    protected $guarded = ['id'];

    protected $casts = [
        'date_naissance'          => 'date',
        'date_registre_commerce'  => 'date',
        'capital_social'          => 'decimal:2',
        'supprime_le'             => 'datetime',
    ];

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function nationalite(): BelongsTo
    {
        return $this->belongsTo(Nationalite::class);
    }

    public function formeJuridique(): BelongsTo
    {
        return $this->belongsTo(FormeJuridique::class);
    }

    public function regimeImposition(): BelongsTo
    {
        return $this->belongsTo(RegimeImposition::class);
    }

    public function coordonneesBancaires(): HasMany
    {
        return $this->hasMany(CoordonneeBancaire::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }

    public function exonerations(): HasMany
    {
        return $this->hasMany(Exoneration::class);
    }

    public function obligations(): HasMany
    {
        return $this->hasMany(Obligation::class);
    }
}
