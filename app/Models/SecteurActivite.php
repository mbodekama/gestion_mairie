<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SecteurActivite extends Model
{
    protected $table = 'secteur_activite';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function activites(): HasMany
    {
        return $this->hasMany(Activite::class);
    }
}
