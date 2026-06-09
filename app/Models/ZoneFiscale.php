<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZoneFiscale extends Model
{
    protected $table = 'zone_fiscale';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }
}
