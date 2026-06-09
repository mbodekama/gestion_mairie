<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NatureTaxe extends Model
{
    protected $table = 'nature_taxe';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function domaineTaxe(): BelongsTo
    {
        return $this->belongsTo(DomaineTaxe::class);
    }

    public function categorieImpotTaxe(): BelongsTo
    {
        return $this->belongsTo(CategorieImpotTaxe::class);
    }

    public function baremesTaxe(): HasMany
    {
        return $this->hasMany(BaremeTaxe::class);
    }

    public function baremesCotisationFonciere(): HasMany
    {
        return $this->hasMany(BaremeCotisationFonciere::class);
    }

    public function emissionsTaxe(): HasMany
    {
        return $this->hasMany(EmissionTaxe::class);
    }

    public function obligations(): HasMany
    {
        return $this->hasMany(Obligation::class);
    }

    public function lignesExoneration(): HasMany
    {
        return $this->hasMany(LigneExoneration::class);
    }
}
