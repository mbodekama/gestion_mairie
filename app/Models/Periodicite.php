<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodicite extends Model
{
    protected $table = 'periodicite';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function baremesTaxe(): HasMany
    {
        return $this->hasMany(BaremeTaxe::class);
    }

    public function emissionsTaxe(): HasMany
    {
        return $this->hasMany(EmissionTaxe::class);
    }

    public function obligations(): HasMany
    {
        return $this->hasMany(Obligation::class);
    }
}
