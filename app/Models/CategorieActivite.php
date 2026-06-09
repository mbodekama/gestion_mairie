<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieActivite extends Model
{
    protected $table = 'categorie_activite';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }

    public function baremesTaxe(): HasMany
    {
        return $this->hasMany(BaremeTaxe::class);
    }
}
