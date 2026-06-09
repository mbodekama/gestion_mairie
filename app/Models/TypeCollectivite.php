<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeCollectivite extends Model
{
    protected $table = 'type_collectivite';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function collectivites(): HasMany
    {
        return $this->hasMany(Collectivite::class);
    }

    public function departementsService(): HasMany
    {
        return $this->hasMany(DepartementService::class);
    }

    public function organisations(): HasMany
    {
        return $this->hasMany(Organisation::class);
    }
}
