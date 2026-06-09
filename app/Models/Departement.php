<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    protected $table = 'departement';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function sousPrefectures(): HasMany
    {
        return $this->hasMany(SousPrefecture::class);
    }

    public function recettes(): HasMany
    {
        return $this->hasMany(Recette::class);
    }

    public function collectivites(): HasMany
    {
        return $this->hasMany(Collectivite::class);
    }
}
