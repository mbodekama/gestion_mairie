<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormeJuridique extends Model
{
    protected $table = 'forme_juridique';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function contribuables(): HasMany
    {
        return $this->hasMany(Contribuable::class);
    }
}
