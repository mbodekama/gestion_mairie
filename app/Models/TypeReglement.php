<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeReglement extends Model
{
    protected $table = 'type_reglement';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function reglementsTaxe(): HasMany
    {
        return $this->hasMany(ReglementTaxe::class);
    }
}
