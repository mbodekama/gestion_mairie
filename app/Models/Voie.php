<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voie extends Model
{
    protected $table = 'voie';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }
}
