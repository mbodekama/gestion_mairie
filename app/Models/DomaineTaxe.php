<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DomaineTaxe extends Model
{
    protected $table = 'domaine_taxe';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function naturesTaxe(): HasMany
    {
        return $this->hasMany(NatureTaxe::class);
    }
}
