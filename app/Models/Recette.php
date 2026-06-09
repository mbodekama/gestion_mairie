<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recette extends Model
{
    protected $table = 'recette';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function collectivites(): HasMany
    {
        return $this->hasMany(Collectivite::class);
    }

    public function reglementssTaxe(): HasMany
    {
        return $this->hasMany(ReglementTaxe::class);
    }
}
