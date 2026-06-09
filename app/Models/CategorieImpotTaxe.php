<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieImpotTaxe extends Model
{
    protected $table = 'categorie_impot_taxe';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function naturesTaxe(): HasMany
    {
        return $this->hasMany(NatureTaxe::class);
    }
}
