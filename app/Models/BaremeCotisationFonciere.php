<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BaremeCotisationFonciere extends Model
{
    protected $table = 'bareme_cotisation_fonciere';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'ca_borne_inf'  => 'decimal:2',
        'ca_borne_sup'  => 'decimal:2',
        'montant_zone1' => 'decimal:2',
        'montant_zone2' => 'decimal:2',
        'forfaitaire'   => 'boolean',
    ];

    public function natureTaxe(): BelongsTo
    {
        return $this->belongsTo(NatureTaxe::class);
    }

    public function activite(): BelongsTo
    {
        return $this->belongsTo(Activite::class);
    }

    public function periodicite(): BelongsTo
    {
        return $this->belongsTo(Periodicite::class);
    }

    public function emissionsCotisationFonciere(): HasMany
    {
        return $this->hasMany(EmissionCotisationFonciere::class, 'bareme_cotisation_id');
    }
}
