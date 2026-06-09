<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciceFiscal extends Model
{
    protected $table = 'exercice_fiscal';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'cloture'    => 'boolean',
    ];

    public function collectivite(): BelongsTo
    {
        return $this->belongsTo(Collectivite::class);
    }

    public function emissionsTaxe(): HasMany
    {
        return $this->hasMany(EmissionTaxe::class);
    }

    public function emissionsCotisationFonciere(): HasMany
    {
        return $this->hasMany(EmissionCotisationFonciere::class);
    }

    public function reglementsTaxe(): HasMany
    {
        return $this->hasMany(ReglementTaxe::class);
    }
}
