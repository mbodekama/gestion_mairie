<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $table = 'region';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function departements(): HasMany
    {
        return $this->hasMany(Departement::class);
    }

    public function collectivites(): HasMany
    {
        return $this->hasMany(Collectivite::class);
    }
}
