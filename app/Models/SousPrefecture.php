<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SousPrefecture extends Model
{
    protected $table = 'sous_prefecture';
    public $timestamps = false;
    protected $guarded = ['id'];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class);
    }
}
