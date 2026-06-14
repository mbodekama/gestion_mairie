<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatutContribuable extends Model
{
    protected $table = 'statut_contribuable';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function contribuables(): HasMany
    {
        return $this->hasMany(Contribuable::class, 'statut', 'code');
    }
}
