<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Banque extends Model
{
    protected $table = 'banque';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function coordonneesBancaires(): HasMany
    {
        return $this->hasMany(CoordonneeBancaire::class);
    }

    public function reglementsTaxe(): HasMany
    {
        return $this->hasMany(ReglementTaxe::class);
    }
}
