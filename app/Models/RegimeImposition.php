<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RegimeImposition extends Model
{
    protected $table = 'regime_imposition';
    public $timestamps = false;
    protected $guarded = ['id'];

    protected $casts = [
        'ca_borne_inf' => 'decimal:2',
        'ca_borne_sup' => 'decimal:2',
    ];

    public function contribuables(): HasMany
    {
        return $this->hasMany(Contribuable::class);
    }
}
