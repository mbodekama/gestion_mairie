<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypePersonne extends Model
{
    protected $table = 'type_personne';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function contribuables(): HasMany
    {
        return $this->hasMany(Contribuable::class, 'type_personne', 'code');
    }
}
