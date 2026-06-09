<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activite extends Model
{
    protected $table = 'activite';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function secteurActivite(): BelongsTo
    {
        return $this->belongsTo(SecteurActivite::class);
    }

    public function categorieActivite(): BelongsTo
    {
        return $this->belongsTo(CategorieActivite::class);
    }

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }

    public function baremesCotisationFonciere(): HasMany
    {
        return $this->hasMany(BaremeCotisationFonciere::class);
    }
}
