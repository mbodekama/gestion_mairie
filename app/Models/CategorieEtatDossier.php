<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorieEtatDossier extends Model
{
    protected $table = 'categorie_etat_dossier';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function familleEtatDossier(): BelongsTo
    {
        return $this->belongsTo(FamilleEtatDossier::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }
}
