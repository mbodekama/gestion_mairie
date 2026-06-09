<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FamilleEtatDossier extends Model
{
    protected $table = 'famille_etat_dossier';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function categoriesEtatDossier(): HasMany
    {
        return $this->hasMany(CategorieEtatDossier::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }
}
