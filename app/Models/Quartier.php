<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quartier extends Model
{
    protected $table = 'quartier';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function voies(): HasMany
    {
        return $this->hasMany(Voie::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }
}
